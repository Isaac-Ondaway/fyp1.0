<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{

    // public function index(Request $request)
    // {
    //     $this->authorize('viewAny', Program::class);
        
    //     $batchID = $request->input('batchID');
    //     $facultyID = $request->input('facultyID');

    //     // Fetch batches and faculties for filters
    //     $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    //     $faculties = User::whereHas('roles', function($query) {
    //         $query->where('type', 'Faculty');
    //     })->get();

    //     // Initialize the query builder
    //     $query = Program::query();

    //     // Apply batch filter if selected
    //     if ($batchID) {
    //         $query->where('batchID', $batchID);
    //     }

    //     // If the user is an admin, allow them to filter by facultyID or see all programs
    //     if (Auth::user()->hasRole('admin')) {
    //         if ($facultyID) {
    //             $query->where('facultyID', $facultyID);
    //         }
    //     } else if (Auth::user()->hasRole('faculty')) {
    //         // If the user is a faculty member, restrict to their own programs
    //         $query->where('facultyID', Auth::id());
    //     }

    //     // Get the programs and sort them by batch start date
    //     $programs = $query->get()->sortByDesc(function ($program) {
    //         return optional($program->batch)->batchStartDate;
    //     })->groupBy(['facultyID', 'batchID']);

    //     return view('programs.index', compact('programs', 'batches', 'faculties', 'batchID', 'facultyID'));
    // }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);
    
        // If the user is an admin, show all programs; otherwise, show only the user’s programs
        $query = Auth::user()->hasRole('admin') ? Program::query() : Program::where('facultyID', Auth::id());
    
        // Get programs grouped by faculty for the simplified view
        $programs = $query->select('programID', 'programName', 'levelEdu', 'programStatus', 'facultyID')
                          ->with('faculty')
                          ->get()
                          ->groupBy('facultyID');
    
        // Fetch all batches
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        return view('programs.index', compact('programs', 'batches'));
    }
    

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        // Authorize the request
        $this->authorize('create', Program::class);
    
        // Get the faculty name from the authenticated user
        $facultyName = Auth::user()->name;
    
        // Retrieve all batches from the database
        $batches = Batch::all();
    
        // Pass both facultyName and batches to the view
        return view('programs.create', compact('facultyName', 'batches'));
    }
    
    /**
     * Store a newly created program in the database.
     */
    public function store(Request $request)
    {
        // Authorize the request
        $this->authorize('create', Program::class);
    
        // Validate the request input
        $request->validate([
            'programID' => 'required|string|max:255|unique:programs,programID',
            'batchID' => 'required|exists:batches,batchID',
            'programName' => 'required|string|max:255',
            'programSem' => 'required|integer',
            'levelEdu' => 'required|string|in:Diploma,Undergraduate,Postgraduate',
            'NEC' => 'required|string|in:Code1,Code2,Code3',
            'programFee' => 'required|string',  // Changed to string since it allows detailed descriptions
            'programDesc' => 'nullable|string|max:1000',
            'studyProgram' => 'required|string|max:255',
            'isInterviewExam' => 'required|boolean',
            'isUjianMedsi' => 'required|boolean',
            'isRayuan' => 'required|boolean',
            'isDDegree' => 'required|boolean',
            'learnMod' => 'required|boolean',
            'isBumiputera' => 'required|boolean',
            'isTEVT' => 'required|boolean',
            'isKompetitif' => 'required|boolean',
            'isBTECH' => 'required|boolean',
            'isOKU' => 'required|boolean',
        ]);
    
        // Create a new program record
        Program::create([
            'programID' => $request->input('programID'),
            'batchID' => $request->input('batchID'),
            'facultyID' => Auth::id(),
            'programName' => $request->input('programName'),
            'programSem' => $request->input('programSem'),
            'levelEdu' => $request->input('levelEdu'),
            'NEC' => $request->input('NEC'),
            'programFee' => $request->input('programFee'),
            'programStatus' => 'Pending', // Default status
            'programDesc' => $request->input('programDesc'),
            'studyProgram' => $request->input('studyProgram'),
            'isInterviewExam' => $request->input('isInterviewExam'),
            'isUjianMedsi' => $request->input('isUjianMedsi'),
            'isRayuan' => $request->input('isRayuan'),
            'isDDegree' => $request->input('isDDegree'),
            'learnMod' => $request->input('learnMod'),
            'isBumiputera' => $request->input('isBumiputera'),
            'isTEVT' => $request->input('isTEVT'),
            'isKompetitif' => $request->input('isKompetitif'),
            'isBTECH' => $request->input('isBTECH'),
            'isOKU' => $request->input('isOKU'),
        ]);
    
        // Redirect to the programs index with a success message
        return redirect()->route('programs.index')->with('success', 'Program created successfully.');
    }
    
    /**
     * Show the form for editing the specified program.
     */
    public function edit($programID, $batchID)
    {
        $program = Program::where('programID', $programID)->where('batchID', $batchID)->firstOrFail();
        $this->authorize('update', $program);
    
        return view('programs.edit', compact('program'));
    }
    
    /**
     * Update the specified program in the database.
     */
    public function update(Request $request, $programID, $batchID)
    {
        // Find the program by programID and batchID
        $program = Program::where('programID', $programID)
                        ->where('batchID', $batchID)
                        ->firstOrFail();
    
        $this->authorize('update', $program);
    
        // Validate the request input
        $data = $request->validate([
            'programID' => 'required|string|max:255|unique:programs,programID,' . $program->programID . ',programID',
            'programName' => 'required|string|max:255',
            'programSem' => 'required|integer',
            'levelEdu' => 'required|string|in:Diploma,Undergraduate,Postgraduate',
            'NEC' => 'required|string|in:Code1,Code2,Code3',
            'programFee' => 'required|string',
            'programDesc' => 'nullable|string|max:1000',
            'studyProgram' => 'required|string|max:255',
            'isInterviewExam' => 'required|boolean',
            'isUjianMedsi' => 'required|boolean',
            'isRayuan' => 'required|boolean',
            'isDDegree' => 'required|boolean',
            'learnMod' => 'required|boolean',
            'isBumiputera' => 'required|boolean',
            'isTEVT' => 'required|boolean',
            'isKompetitif' => 'required|boolean',
            'isBTECH' => 'required|boolean',
            'isOKU' => 'required|boolean',
        ]);
    
        // Only admins can update the program status
        if (Auth::user()->hasRole('admin')) {
            $data['programStatus'] = $request->input('programStatus');
        }
    
        // Update the program with the validated data
        $program->update($data);
    
        // Redirect back to the programs index with a success message
        return redirect()->route('programs.index')->with('success', 'Program updated successfully.');
    }
    
    /**
     * Remove the specified program from the database.
     */
    public function destroy($programID, $batchID)
    {
        $program = Program::where('programID', $programID)->where('batchID', $batchID)->firstOrFail();
        $this->authorize('delete', $program);
    
        $program->delete();
    
        return redirect()->route('programs.index')->with('success', 'Program deleted successfully.');
    }
    
    public function getProgramsByBatch($batchID)
    {
        $user = Auth::user();
    
        // Retrieve the programs based on the user role
        if ($user->hasRole('admin')) {
            // Admins can see all programs grouped by facultyID and batchID
            $programs = Program::where('batchID', $batchID)
                               ->get()
                               ->groupBy(['facultyID', 'batchID']);
        } elseif ($user->hasRole('faculty')) {
            // Faculty members can only see their own programs
            $programs = Program::where('batchID', $batchID)
                               ->where('facultyID', $user->id)
                               ->get()
                               ->groupBy(['facultyID', 'batchID']);
        } else {
            // If the user has no relevant role, return no programs
            $programs = collect(); // Empty collection
        }
    
        // Fetch additional data if needed
        $faculties = User::whereHas('roles', function($query) {
            $query->where('type', 'Faculty');
        })->get();
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        // Render the view with a noProgramsMessage if programs are empty
        $noProgramsMessage = $programs->isEmpty() ? 'No programs found for the selected batch.' : null;
    
        return view('programs.partials.program_list', compact('programs', 'faculties', 'batches', 'noProgramsMessage'));
    }
    
    
    

}

