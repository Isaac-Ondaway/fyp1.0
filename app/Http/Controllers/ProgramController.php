<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Faculty;
use App\Models\EntryLevelCategory;
use App\Models\ProgramEntryLevelMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Program::class);
    
        $user = Auth::user();
    
        if ($user->hasRole('admin')) {
            // Admin can see all programs
            $programs = Program::with('faculty')
                ->select('programID', 'programName', 'levelEdu', 'programStatus', 'facultyID', 'batchID')
                ->get()
                ->groupBy('facultyID');
        } else {
            // Faculty user can see only programs belonging to their faculty
            $programs = Program::with('faculty')
                ->where('facultyID', $user->facultyID)
                ->select('programID', 'programName', 'levelEdu', 'programStatus', 'facultyID', 'batchID')
                ->get()
                ->groupBy('facultyID');
        }
    
        $faculties = Faculty::all(); // Fetch all faculties

        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        return view('programs.index', compact('programs', 'batches', 'faculties'));
    }
    
    

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        $this->authorize('create', Program::class);
    
        $user = Auth::user();
        $faculties = [];
    
        if ($user->hasRole('admin')) {
            // Admin can choose from all faculties
            $faculties = Faculty::all();
        }
    
        // Retrieve all batches from the database
        $batches = Batch::all();
    
        return view('programs.create', compact('faculties', 'batches', 'user'));
    }
    
    
    /**
     * Store a newly created program in the database.
     */
    public function store(Request $request)
    {
        // Authorize the request
        $this->authorize('create', Program::class);
    
        $user = Auth::user();
    
        // Validation rules
        $rules = [
            'programID' => 'required|string|max:255|unique:programs,programID',
            'batchID' => 'required|exists:batches,batchID',
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
        ];
    
        // If the user is an admin, include facultyID in validation
        if ($user->hasRole('admin')) {
            $rules['facultyID'] = 'required|exists:faculty,id';
            $rules['programStatus'] = 'required|in:Pending,Approved,Rejected'; // Optional for admin
        }
    
        // Validate the request input
        $validatedData = $request->validate($rules);
    
        // Automatically assign facultyID for non-admin users
        if (!$user->hasRole('admin')) {
            $validatedData['facultyID'] = $user->facultyID;
            $validatedData['programStatus'] = 'Pending'; // Non-admin users can't set status
        }
    
        // Create a new program record
        Program::create($validatedData);
    
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
    
        // Authorize the request
        $this->authorize('update', $program);
    
        $user = Auth::user();
    
        // Validation rules
        $rules = [
            'programID' => 'required|string|max:255|unique:programs,programID,' . $program->programID . ',programID,batchID,' . $program->batchID,
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
        ];
    
        // Add admin-specific validation rules
        if ($user->hasRole('admin')) {
            $rules['facultyID'] = 'required|exists:faculties,id';
            $rules['programStatus'] = 'required|in:Pending,Approved,Rejected';
        }
            
        // Validate the request input
        $validatedData = $request->all();
        // Ensure `facultyID` is excluded for non-admin users
        if (!$user->hasRole('admin')) {
            unset($validatedData['facultyID']);
        }
        
    
        try {
            // Update the program with the validated data
            $program->update($validatedData);
            \Log::info('Program updated successfully.');
            return redirect()->route('programs.index')->with('success', 'Program updated successfully.');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            \Log::error('Program update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating the program.']);
        }
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
    
        // Check if the user is an admin
        if ($user->hasRole('admin')) {
            // Admins can see all programs grouped by facultyID and batchID
            $programs = Program::where('batchID', $batchID)
                ->with('faculty')
                ->get()
                ->groupBy(['facultyID', 'batchID']);
        } elseif ($user->hasRole('faculty')) {
            // Faculty members can only see their own faculty's programs
            $programs = Program::where('batchID', $batchID)
                ->where('facultyID', $user->facultyID)
                ->with('faculty')
                ->get()
                ->groupBy(['facultyID', 'batchID']);
        } else {
            // If the user has no relevant role, return an empty collection
            $programs = collect();
        }
    
        // Fetch additional data if needed
        $faculties = User::whereHas('roles', function ($query) {
            $query->where('type', 'Faculty');
        })->get();
    
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        // Render the view with a noProgramsMessage if programs are empty
        $noProgramsMessage = $programs->isEmpty() ? 'No programs found for the selected batch.' : null;
    
        return view('programs.partials.program_list', compact('programs', 'faculties', 'batches', 'noProgramsMessage'));
    }
    


    public function updateEntryLevels(Request $request)
    {
        $data = $request->input('entry_levels', []);
        $batchID = $request->get('batch', Batch::latest()->first()->batchID);
    
        // Process each program's entry levels
        foreach ($data as $programID => $categories) {
            // Remove existing mappings for this program and batch
            ProgramEntryLevelMapping::where('programID', $programID)
                ->where('batchID', $batchID)
                ->delete();
    
            // Add new mappings
            foreach ($categories as $categoryID => $value) {
                ProgramEntryLevelMapping::create([
                    'programID' => $programID,
                    'batchID' => $batchID,
                    'entry_level_category_id' => $categoryID,
                    'is_offered' => true,
                ]);
            }
        }
    
        return redirect()->route('programs.manage_entry_levels', ['batch' => $batchID])
            ->with('success', 'Entry levels updated successfully!');
    }
    

    public function manageEntryLevels(Request $request)
    {
        $user = auth()->user();
    
        // Get the selected batch or default to the latest
        $selectedBatch = $request->get('batch', Batch::latest()->first()->batchID);
    
        $selectedFaculty = null; // Initialize the variable for all users
    
        // Fetch programs based on user role
        if ($user->hasRole('admin')) {
            // Admin: Fetch all programs and filter by faculty if selected
            $selectedFaculty = $request->get('faculty', null);
            $programs = Program::where('batchID', $selectedBatch)
                ->when($selectedFaculty, function ($query, $selectedFaculty) {
                    return $query->where('facultyID', $selectedFaculty);
                })
                ->get();
        } else {
            // Faculty: Fetch programs belonging to their faculty
            $programs = Program::where('batchID', $selectedBatch)
                ->where('facultyID', $user->facultyID)
                ->get();
        }
    
        // Fetch all entry-level categories
        $categories = EntryLevelCategory::all();
    
        // Fetch current program-entry level mappings
        $programEntryLevels = ProgramEntryLevelMapping::where('batchID', $selectedBatch)
            ->get()
            ->groupBy('programID');
    
        // Fetch all batches for the dropdown
        $batches = Batch::all();
    
        // Fetch all faculties for filtering (for admins)
        $faculties = Faculty::all(); // Adjust the model if your faculties are stored differently
    
        return view('programs.manage_entry_levels', compact(
            'programs',
            'categories',
            'programEntryLevels',
            'batches',
            'selectedBatch',
            'faculties',
            'selectedFaculty' // Always include this variable, even if null
        ));
    }
    
    

}

