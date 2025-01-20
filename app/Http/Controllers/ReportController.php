<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ProgramEntryLevel;
use App\Models\EntryLevelCategory;
use App\Models\ProgramEntryLevelMapping;
use App\Models\Program;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function combinedReport(Request $request)
    {
        try {

            $user = auth()->user();
            $isAdmin = $user->hasRole('admin');

            // Fetch the latest batch
            $latestBatch = Batch::orderBy('batchStartDate', 'desc')->first();
            if (!$latestBatch) {
                throw new \Exception('No batches found.');
            }

            // Fetch batches and faculties
            $batches = Batch::orderBy('batchStartDate', 'asc')->get();
            $faculties = $isAdmin ? Faculty::all() : Faculty::where('id', $user->facultyID)->get();

            // Prepare batch entry-level data
            $batchEntryLevelData = $batches->map(function ($batch) use ($isAdmin, $user) {
                $entryLevelsQuery = ProgramEntryLevel::join('entry_levels', 'program_entry_levels.entry_level_id', '=', 'entry_levels.entryLevelID')
                    ->where('program_entry_levels.batch_id', $batch->batchID);

                if (!$isAdmin) {
                    $entryLevelsQuery->whereHas('program', function ($query) use ($user) {
                        $query->where('facultyID', $user->facultyID);
                    });
                }

                $entryLevels = $entryLevelsQuery->selectRaw('
                    SUM(CASE WHEN entry_levels.entryLevelID = 1 THEN intake_count ELSE 0 END) as STPM,
                    SUM(CASE WHEN entry_levels.entryLevelID = 2 THEN intake_count ELSE 0 END) as STAM,
                    SUM(CASE WHEN entry_levels.entryLevelID = 3 THEN intake_count ELSE 0 END) as Diploma
                ')->first();

                return [
                    'batchName' => $batch->batchName,
                    'entryLevels' => [
                        'STPM' => $entryLevels->STPM ?? 0,
                        'STAM' => $entryLevels->STAM ?? 0,
                        'Diploma' => $entryLevels->Diploma ?? 0,
                    ],
                ];
            });

            // Fetch programs with optional filters
            $programQuery = Program::query();
            if ($request->filled('batchID')) {
                $programQuery->where('batchID', $request->batchID);
            }
            if ($request->filled('facultyID')) {
                $programQuery->where('facultyID', $request->facultyID);
            }
            $programs = $programQuery->with('faculty', 'batch')->get();

            // Fetch intake count summary
            $intakeCounts = ProgramEntryLevel::query()
            ->join('entry_levels', 'program_entry_levels.entry_level_id', '=', 'entry_levels.entryLevelID')
            ->when($request->filled('batchID'), fn($query) => $query->where('program_entry_levels.batch_id', $request->batchID))
            ->when($request->filled('facultyID'), fn($query) => $query->whereHas('program', fn($q) => $q->where('facultyID', $request->facultyID)))
            ->selectRaw('
                program_id,
                SUM(CASE WHEN entry_levels.entryLevelID = 1 THEN intake_count ELSE 0 END) as STPM,
                SUM(CASE WHEN entry_levels.entryLevelID = 2 THEN intake_count ELSE 0 END) as STAM,
                SUM(CASE WHEN entry_levels.entryLevelID = 3 THEN intake_count ELSE 0 END) as Diploma
            ')
            ->groupBy('program_id')
            ->get();

// Fetch checkbox table data
$categories = EntryLevelCategory::all(); 

$programEntryLevels = ProgramEntryLevelMapping::query()
    ->when($request->filled('batchID'), function ($query) use ($request) {
        $query->where('batchID', $request->batchID); // Filter by batch ID
    })
    ->when($request->filled('facultyID'), function ($query) use ($request) {
        // Ensure facultyID is valid
        if (is_numeric($request->facultyID)) {
            $query->whereHas('program', function ($subQuery) use ($request) {
                $subQuery->where('facultyID', $request->facultyID); // Filter by faculty ID
            });
        } else {
            \Log::error('Invalid facultyID provided.', ['facultyID' => $request->facultyID]);
        }
    })
    ->get()
    ->groupBy('programID'); // Group results by programID





 




            // Handle AJAX request
            if ($request->ajax()) {
                $programs = $programQuery->with('faculty', 'batch')->get();
            
                // Check if programs exist
                if ($programs->isEmpty()) {
                    return response()->json([
                        'html' => '<p class="text-center text-gray-500">No programs found for the selected filters.</p>',
                    ]);
                }
            
                return response()->json([
                    'html' => view('reports.partials.report-list', compact('programs', 'intakeCounts', 'categories', 'programEntryLevels'))->render(),
                ]);
            }
            

            // Fetch program status counts
            $programStatusCounts = Program::where('batchID', $latestBatch->batchID)
                ->select('facultyID', 'programStatus', \DB::raw('COUNT(*) as count'))
                ->groupBy('facultyID', 'programStatus')
                ->get()
                ->groupBy('facultyID');

            // Prepare faculty batch intake data
            $facultyBatchData = [];
            foreach ($faculties as $faculty) {
                $facultyPrograms = Program::where('facultyID', $faculty->id)->pluck('programID');
                $facultyBatchData[$faculty->id] = $batches->map(function ($batch) use ($facultyPrograms) {
                    $entryLevels = ProgramEntryLevel::join('entry_levels', 'program_entry_levels.entry_level_id', '=', 'entry_levels.entryLevelID')
                        ->where('program_entry_levels.batch_id', $batch->batchID)
                        ->whereIn('program_entry_levels.program_id', $facultyPrograms)
                        ->selectRaw('
                            SUM(CASE WHEN entry_levels.entryLevelID = 1 THEN intake_count ELSE 0 END) as STPM,
                            SUM(CASE WHEN entry_levels.entryLevelID = 2 THEN intake_count ELSE 0 END) as STAM,
                            SUM(CASE WHEN entry_levels.entryLevelID = 3 THEN intake_count ELSE 0 END) as Diploma
                        ')
                        ->first();

                    return [
                        'batchName' => $batch->batchName,
                        'entryLevels' => [
                            'STPM' => $entryLevels->STPM ?? 0,
                            'STAM' => $entryLevels->STAM ?? 0,
                            'Diploma' => $entryLevels->Diploma ?? 0,
                        ],
                    ];
                });
            }

            return view('reports.combined', [
                'currentBatch' => $latestBatch,
                'currentBatchIntake' => ProgramEntryLevel::where('batch_id', $latestBatch->batchID)->sum('intake_count'),
                'previousBatchIntakes' => $batches,
                'batchEntryLevelData' => $batchEntryLevelData,
                'programStatusCounts' => $programStatusCounts,
                'faculties' => $faculties,
                'facultyBatchData' => $facultyBatchData,
                'programs' => $programs,
                'batches' => $batches,
                'intakeCounts' => $intakeCounts,
                'categories' => $categories,
                'programEntryLevels' => $programEntryLevels,
            ]);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return view('reports.combined', [
                'currentBatch' => null,
                'currentBatchIntake' => 0,
                'previousBatchIntakes' => [],
                'batchEntryLevelData' => [],
                'programStatusCounts' => [],
                'faculties' => [],
                'facultyBatchData' => [],
                'programs' => [],
                'batches' => [],
                'intakeCounts' => [],
                'categories' => [],
                'programEntryLevels' => [],
            ])->with('message', $e->getMessage());
        }
    }
}
