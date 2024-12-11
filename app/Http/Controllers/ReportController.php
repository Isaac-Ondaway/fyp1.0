<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ProgramEntryLevel;
use App\Models\Program;
use App\Models\Faculty;

class ReportController extends Controller
{
    public function combinedReport()
    {
        // Fetch the latest batch
        $latestBatch = Batch::orderBy('batchStartDate', 'desc')->first();

        if (!$latestBatch) {
            return view('reports.combined', [
                'currentBatch' => null,
                'currentBatchIntake' => 0,
                'previousBatchIntakes' => [],
                'batchEntryLevelData' => [],
                'programStatusCounts' => [],
                'faculties' => [],
            ])->with('message', 'No batches found.');
        }

        // Fetch the latest two batches
        $batches = Batch::orderBy('batchStartDate', 'desc')->take(2)->get();

        // Prepare batch entry-level data
        $batchEntryLevelData = $batches->map(function ($batch) {
            $entryLevels = ProgramEntryLevel::join('entry_levels', 'program_entry_levels.entry_level_id', '=', 'entry_levels.entryLevelID')
                ->where('program_entry_levels.batch_id', $batch->batchID)
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

        // Fetch program status counts grouped by faculty for the latest batch
        $programStatusCounts = Program::where('batchID', $latestBatch->batchID)
            ->select('facultyID', 'programStatus', \DB::raw('COUNT(*) as count'))
            ->groupBy('facultyID', 'programStatus')
            ->get()
            ->groupBy('facultyID');

        // Fetch faculty names
        $faculties = Faculty::whereIn('id', $programStatusCounts->keys())->get();

        return view('reports.combined', [
            'currentBatch' => $latestBatch,
            'currentBatchIntake' => ProgramEntryLevel::where('batch_id', $latestBatch->batchID)->sum('intake_count'),
            'previousBatchIntakes' => $batches,
            'batchEntryLevelData' => $batchEntryLevelData,
            'programStatusCounts' => $programStatusCounts,
            'faculties' => $faculties,
        ]);
    }
}
