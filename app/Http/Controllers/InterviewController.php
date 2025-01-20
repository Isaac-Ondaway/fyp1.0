<?php
namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InterviewController extends Controller
{
    public function create()
    {
        $user = Auth::user();
    
        // Fetch programs based on role
        if ($user->hasRole('admin')) {
            $programs = Program::select('programID', 'programName')->groupBy('programID', 'programName')->get();
        } else {
            $programs = Program::where('facultyID', $user->faculty->id)
                ->select('programID', 'programName')
                ->groupBy('programID', 'programName')
                ->get();
        }
    
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        return view('interviews.create', compact('programs', 'batches'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'programID' => 'required|string|max:255',
            'batchID' => 'required|integer',
            'intervieweeName' => 'required|string|max:255',
            'contactNumber' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Interview::create([
            'programID' => $request->programID,
            'batchID' => $request->batchID,
            'intervieweeName' => $request->intervieweeName,
            'contactNumber' => $request->contactNumber,
            'email' => $request->email,
        ]);

        return redirect()->route('interviews.index')->with('success', 'Interview added successfully.');
    }

    public function checkDuplicateContact(Request $request)
    {
        $isDuplicate = Interview::where('contactNumber', $request->contactNumber)
            ->where('batchID', $request->batchID)
            ->where('programID', $request->programID)
            ->exists();
    
        return response()->json(['isDuplicate' => $isDuplicate]);
    }
    
    public function checkDuplicateEmail(Request $request)
    {
        $isDuplicate = Interview::where('email', $request->email)
            ->where('batchID', $request->batchID)
            ->where('programID', $request->programID)
            ->exists();
    
        return response()->json(['isDuplicate' => $isDuplicate]);
    }
    

    public function update(Request $request, $id)
    {
        Log::debug($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ]);
    
        $interview = Interview::findOrFail($id);
        $interview->update([
            'intervieweeName' => $request->name,
            'contactNumber' => $request->contact_number,
            'email' => $request->email,
        ]);
        
    
        return response()->json(['success' => true]);
    }
    
    


    public function index(Request $request)
    {
        $user = Auth::user();
        $batchID = $request->input('batchID');
        $facultyID = $request->input('faculty_id');
        $search = $request->input('search'); // Search query
    
        $query = Interview::query();
    
        // Filter by batch
        if ($batchID) {
            $query->where('batchID', $batchID);
        }
    
        // Filter by faculty (admin only)
        if ($facultyID) {
            $query->whereHas('program', function ($q) use ($facultyID) {
                $q->where('facultyID', $facultyID);
            });
        } elseif ($user->hasRole('faculty')) {
            // Filter interviews for faculty's own programs
            $query->whereHas('program', function ($q) use ($user) {
                $q->where('facultyID', $user->faculty->id);
            });
        }
    
        // Search by intervieweeName, contactNumber, or programName
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('intervieweeName', 'LIKE', "%$search%") // Fixed column name
                  ->orWhere('contactNumber', 'LIKE', "%$search%") // Fixed column name
                  ->orWhereHas('program', function ($subQuery) use ($search) {
                      $subQuery->where('programName', 'LIKE', "%$search%");
                  });
            });
        }
    
        $interviews = $query->get();
        
        
        // Handle AJAX request
        if ($request->ajax()) {
            return view('interviews.partials.interview-list', compact('interviews'))->render();
        }
    
        $batches = Batch::all();
        $faculties = $user->hasRole('admin') ? Faculty::all() : null;

        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        return view('interviews.index', compact('interviews', 'batches', 'faculties', 'batchID', 'facultyID'));
    }
    
    
    
    
    

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $path = $request->file('file')->store('temp');
        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));
        $rows = array_filter($rows, fn($row) => !empty(array_filter($row)));
        $headers = array_shift($rows);

        return view('interviews.review-csv', compact('rows', 'headers', 'path'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'programID' => 'required|string|max:255',
            'batchID' => 'required|integer',
            'interviewees.*.intervieweeName' => 'required|string|max:255',
            'interviewees.*.contactNumber' => 'required|string|max:20',
            'interviewees.*.email' => 'required|email|max:255',
        ]);

        foreach ($request->interviewees as $interviewee) {
            Interview::create([
                'programID' => $request->programID,
                'batchID' => $request->batchID,
                'intervieweeName' => $interviewee['intervieweeName'],
                'contactNumber' => $interviewee['contactNumber'],
                'email' => $interviewee['email'],
            ]);
        }

        return redirect()->route('interviews.index')->with('success', 'Interviewees added successfully.');
    }

    public function bulkStoreCsv(Request $request)
    {
        $path = $request->input('filePath');
        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));
        array_shift($rows);
    
        $user = Auth::user();
    
        foreach ($rows as $row) {
            if (count($row) >= 4 && !empty(array_filter($row))) {
                $programExists = Program::where('programID', $row[0])
                    ->where('batchID', $row[1]);
    
                // Ensure faculty restriction for non-admins
                if (!$user->hasRole('admin')) {
                    $programExists->where('facultyID', $user->faculty->id);
                }
    
                $programExists = $programExists->exists();
    
                if ($programExists) {
                    Interview::create([
                        'programID' => $row[0],
                        'batchID' => $row[1],
                        'intervieweeName' => $row[2],
                        'contactNumber' => $row[3],
                        'email' => $row[4],
                    ]);
                }
            }
        }
    
        return redirect()->route('interviews.index')->with('success', 'Interviewees added successfully!');
    }

    public function uploadCsv()
    {
        return view('interviews.uploadCsv');
    }

    
    
    

    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);
        $interview->delete();
    
        return redirect()->back()->with('success', 'Interview deleted successfully.');
    }
    


    public function getBatchesForProgram($programID)
    {
        // Ensure the program has associated batches
        $batches = Batch::whereHas('programs', function ($query) use ($programID) {
            $query->where('programID', $programID);
        })->get();

        return response()->json($batches); // Ensure JSON is returned
    }


    public function getProgramsForBatch($batchID)
    {
        $user = Auth::user();
    
        if ($user->hasRole('admin')) {
            $programs = Program::where('batchID', $batchID)->get();
        } else {
            $programs = Program::where('batchID', $batchID)
                ->where('facultyID', $user->faculty->id)
                ->get();
        }
    
        return response()->json($programs);
    }
    
    

}
