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
    public function index()
    {
        $user = Auth::user(); // Get the current logged-in user

        // Check if the user has the 'admin' role
        if ($user->hasRole('admin')) {
            // Admin can see all programs and all existing program entry levels
            $programs = Program::all();
            $programEntryLevels = ProgramEntryLevel::all();
        } else {
            // Faculty member can only see programs they own and related program entry levels
            $programs = Program::where('facultyID', $user->id)->get();
            $programEntryLevels = ProgramEntryLevel::whereHas('program', function ($query) use ($user) {
                $query->where('facultyID', $user->id);
            })->get();
        }

        $batches = Batch::all();
        $entryLevels = EntryLevel::all();

        return view('intakes.index', compact('programs', 'batches', 'entryLevels', 'programEntryLevels'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'program_id' => 'required|array',
            'batch_id' => 'required|array',
            'entry_level_id' => 'required|array',
            'intake_count' => 'required|array',
        ]);
    
    
        foreach ($request->program_id as $key => $programID) {
    
            ProgramEntryLevel::updateOrCreate(
                [
                    'program_id' => $programID,
                    'batch_id' => $request->batch_id[$key],
                    'entry_level_id' => $request->entry_level_id[$key],
                ],
                [
                    'intake_count' => $request->intake_count[$key],
                ]
            );
        }
    
        return back()->with('success', 'Data successfully saved!');
    }


    








}
