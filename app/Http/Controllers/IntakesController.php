<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramEntryLevel;
use App\Models\Program;
use App\Models\EntryLevelCategory;
use App\Models\Batch;
use App\Models\EntryLevel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class IntakesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBatchID = $request->input('batch_id');
    
        // Get batches and entry levels
        $batches = Batch::all();
        $entryLevels = EntryLevel::all();
    
        // Retrieve programs based on user role and selected batch
        if ($user->hasRole('admin')) {
            $programs = Program::when($selectedBatchID, function ($query) use ($selectedBatchID) {
                return $query->where('batchID', $selectedBatchID);
            })->get();
        } else {
            $programs = Program::where('facultyID', $user->id)
                ->when($selectedBatchID, function ($query) use ($selectedBatchID) {
                    return $query->where('batchID', $selectedBatchID);
                })->get();
        }
    
        // Retrieve existing intake data for each program and entry level
        $existingIntakes = ProgramEntryLevel::where('batch_id', $selectedBatchID)
            ->get()
            ->groupBy('program_id')
            ->mapWithKeys(function ($intakes, $programID) {
                return [$programID => $intakes->keyBy('entry_level_id')->map->intake_count];
            });
    
        return view('intakes.index', compact('programs', 'batches', 'entryLevels', 'selectedBatchID', 'existingIntakes'));
    }
    
    


    public function store(Request $request)
    {
        $batchID = $request->input('batch_id'); // Retrieve the batch ID
        $programID = $request->input('program_id'); // Retrieve the program ID
        $intakes = $request->input('intake'); // This should contain 'stpm', 'stam', and 'diploma' values
    
        // Map entry levels to their corresponding IDs
        $entryLevels = [
            'stpm' => 1, // Replace with actual ID for STPM
            'stam' => 2, // Replace with actual ID for STAM
            'diploma' => 3, // Replace with actual ID for Diploma Setaraf
        ];
    
        // Loop through each intake type and store/update in the database
        foreach ($intakes as $level => $count) {
            if ($count > 0) { // Only store if intake count is greater than zero
                ProgramEntryLevel::updateOrCreate(
                    [
                        'program_id' => $programID,
                        'batch_id' => $batchID,
                        'entry_level_id' => $entryLevels[$level],
                    ],
                    [
                        'intake_count' => $count,
                    ]
                );
            }
        }
    
        return redirect()->back()->with('success', 'Intake numbers saved successfully.');
    }
    
    public function storeAll(Request $request)
    {
        $batchID = $request->input('batch_id');
        $intakes = $request->input('intake');
    
        // Map entry levels to their corresponding IDs
        $entryLevels = [
            'stpm' => 1, // Replace with actual ID for STPM
            'stam' => 2, // Replace with actual ID for STAM
            'diploma' => 3, // Replace with actual ID for Diploma Setaraf
        ];
    
        // Loop through each program's intake data
        foreach ($intakes as $programID => $counts) {
            foreach (['stpm', 'stam', 'diploma'] as $entryLevel) {
                if (isset($counts[$entryLevel]) && $counts[$entryLevel] > 0) {
                    ProgramEntryLevel::updateOrCreate(
                        [
                            'program_id' => $programID,
                            'batch_id' => $batchID,
                            'entry_level_id' => $entryLevels[$entryLevel],
                        ],
                        [
                            'intake_count' => $counts[$entryLevel],
                        ]
                    );
                }
            }
        }
    
        return redirect()->back()->with('success', 'All intake numbers saved successfully.');
    }
    

}
