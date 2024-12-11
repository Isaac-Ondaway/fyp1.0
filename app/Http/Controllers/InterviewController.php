<?php
namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Program;
use App\Models\Batch;
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
    
        $batches = Batch::all();
    
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $batchID = $request->input('batchID');
        $programID = $request->input('programID');
    
        $query = Interview::query();
    
        if ($batchID) {
            $query->where('batchID', $batchID);
        }
    
        if ($programID) {
            $query->where('programID', $programID);
        }
    
        if ($user->hasRole('admin')) {
            $interviews = $query->get();
            $programs = Program::all();
        } elseif ($user->hasRole('faculty')) {
            $query->whereHas('program', function ($query) use ($user) {
                $query->where('facultyID', $user->faculty->id);
            });
            $interviews = $query->get();
            $programs = Program::where('facultyID', $user->faculty->id)->get();
        }
    
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
    
        if ($request->ajax()) {
            return view('interviews.partials.interview-list', compact('interviews'))->render();
        }
    
        return view('interviews.index', compact('interviews', 'batches', 'programs'));
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
            'interviewees.*.email' => 'nullable|email|max:255',
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
                        'email' => $row[4] ?? null,
                    ]);
                }
            }
        }
    
        return redirect()->route('interviews.index')->with('success', 'Interviewees added successfully!');
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
