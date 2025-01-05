<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramEntryLevel;
use App\Models\Program;
use App\Models\EntryLevelCategory;
use App\Models\Batch;
use App\Models\Faculty;
use App\Models\EntryLevel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class IntakesController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     $selectedBatchID = $request->input('batch_id');
    //     $selectedFacultyID = $request->input('faculty_id'); // New filter for faculty

    //     // Get batches, faculties, and entry levels
    //     $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    //     $entryLevels = EntryLevel::all();
    //     $faculties = Faculty::all(); // Retrieve all faculties for the filter dropdown

    //     // Retrieve programs based on user role, selected batch, faculty, and approved status
    //     if ($user->hasRole('admin')) {
    //         // Admin can see all approved programs
    //         $programs = Program::where('programStatus', 'Approved')
    //             ->when($selectedBatchID, function ($query) use ($selectedBatchID) {
    //                 return $query->where('batchID', $selectedBatchID);
    //             })
    //             ->when($selectedFacultyID, function ($query) use ($selectedFacultyID) {
    //                 return $query->where('facultyID', $selectedFacultyID);
    //             })
    //             ->with('faculty') // Add 'faculty' relationship for additional data
    //             ->get();
    //     } else {
    //         // Faculty members can see only their faculty's approved programs
    //         $programs = Program::where('facultyID', $user->faculty->id) // Adjusted to fetch based on faculty relationship
    //             ->where('programStatus', 'Approved')
    //             ->when($selectedBatchID, function ($query) use ($selectedBatchID) {
    //                 return $query->where('batchID', $selectedBatchID);
    //             })
    //             ->with('faculty') // Add 'faculty' relationship for additional data
    //             ->get();
    //     }

    //     // Retrieve existing intake data for each program and entry level
    //     $existingIntakes = ProgramEntryLevel::where('batch_id', $selectedBatchID)
    //         ->get()
    //         ->groupBy('program_id')
    //         ->mapWithKeys(function ($intakes, $programID) {
    //             return [$programID => $intakes->keyBy('entry_level_id')->map->intake_count];
    //         });

    //     return view('intakes.index', compact('programs', 'batches', 'entryLevels', 'faculties', 'selectedBatchID', 'selectedFacultyID', 'existingIntakes'));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedBatchID = $request->input('batch_id');
        $selectedFacultyID = $request->input('faculty_id');

        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
        $entryLevels = EntryLevel::all();
        $faculties = Faculty::all();

        // Retrieve programs based on user role
        $programsQuery = Program::query()
            ->where('programStatus', 'Approved')
            ->when($selectedBatchID, function ($query) use ($selectedBatchID) {
                $query->where('batchID', $selectedBatchID);
            })
            ->when($selectedFacultyID, function ($query) use ($selectedFacultyID) {
                $query->where('facultyID', $selectedFacultyID);
            })
            ->with('faculty'); // Add 'faculty' relationship for additional data

        if (!$user->hasRole('admin')) {
            // Faculty members can see only their programs
            $programsQuery->where('facultyID', $user->faculty->id);
        }

        $programs = $programsQuery->get();

        // Retrieve existing intake data
        $existingIntakes = ProgramEntryLevel::where('batch_id', $selectedBatchID)
            ->get()
            ->groupBy('program_id')
            ->mapWithKeys(function ($intakes, $programID) {
                return [$programID => $intakes->keyBy('entry_level_id')->map->intake_count];
            });

        return view('intakes.index', compact('programs', 'batches', 'entryLevels', 'faculties', 'selectedBatchID', 'selectedFacultyID', 'existingIntakes'));
    }



    // public function store(Request $request)
    // {
    //     $batchID = $request->input('batch_id'); // Retrieve the batch ID
    //     $programID = $request->input('program_id'); // Retrieve the program ID
    //     $intakes = $request->input('intake'); // This should contain 'stpm', 'stam', and 'diploma' values
    
    //     // Map entry levels to their corresponding IDs
    //     $entryLevels = [
    //         'stpm' => 1, // Replace with actual ID for STPM
    //         'stam' => 2, // Replace with actual ID for STAM
    //         'diploma' => 3, // Replace with actual ID for Diploma Setaraf
    //     ];
    
    //     // Loop through each intake type and store/update in the database
    //     foreach ($intakes as $level => $count) {
    //         if ($count > 0) { // Only store if intake count is greater than zero
    //             ProgramEntryLevel::updateOrCreate(
    //                 [
    //                     'program_id' => $programID,
    //                     'batch_id' => $batchID,
    //                     'entry_level_id' => $entryLevels[$level],
    //                 ],
    //                 [
    //                     'intake_count' => $count,
    //                 ]
    //             );
    //         }
    //     }
    
    //     return redirect()->back()->with('success', 'Intake numbers saved successfully.');
    // }

    public function store(Request $request)
{
    $batchID = $request->input('batch_id');
    $programID = $request->input('program_id');
    $intakes = $request->input('intake', []); // Defaults to an empty array

    // Map entry levels to their corresponding IDs
    $entryLevels = [
        'stpm' => 1, // Replace with actual ID for STPM
        'stam' => 2, // Replace with actual ID for STAM
        'diploma' => 3, // Replace with actual ID for Diploma Setaraf
    ];

    foreach ($entryLevels as $level => $entryLevelId) {
        $count = $intakes[$level] ?? 0; // Default to 0 if not set
        if ($count > 0) {
            ProgramEntryLevel::updateOrCreate(
                [
                    'program_id' => $programID,
                    'batch_id' => $batchID,
                    'entry_level_id' => $entryLevelId,
                ],
                [
                    'intake_count' => $count,
                ]
            );
        }
    }

    return redirect()->back()->with('success', 'Intake numbers saved successfully for the selected program.');
}

    //changed
public function storeAll(Request $request)
{
    $batchID = $request->input('batch_id');
    $intakes = $request->input('intake');

    // Check if $intakes is null or empty
    if (!$intakes || !is_array($intakes)) {
        return redirect()->back()->with('error', 'No intake data provided.');
    }

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
