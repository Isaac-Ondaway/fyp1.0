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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IntervieweesImport;


class InterviewController extends Controller
{
    // Show form to create interview
    public function create()
    {
        // Fetch unique programs by programID
        $programs = Program::select('programID', 'programName')
            ->groupBy('programID', 'programName') // Group by programID and programName
            ->get();

        // Fetch all batches (no changes needed here)
        $batches = Batch::all();

        // Return the view with the programs and batches
        return view('interviews.create', compact('programs', 'batches'));
    }



    // Store interview
    public function store(Request $request)
    {
        $request->validate([
            'programID' => 'required',
            'batchID' => 'required',
            'intervieweeName' => 'required|string|max:255',
            'contactNumber' => 'required|string|max:20',
            'interviewStatus' => 'required|in:Pending,Scheduled,Completed,Canceled',
        ]);

        Interview::create([
            'programID' => $request->programID,
            'batchID' => $request->batchID,
            'intervieweeName' => $request->intervieweeName,
            'contactNumber' => $request->contactNumber,
            'interviewStatus' => $request->interviewStatus,
        ]);

        return redirect()->route('interviews.index')->with('success', 'Interview added successfully.');
    }


    // List interviews
    public function index(Request $request)
    {
        $user = Auth::user();
        $batchID = $request->input('batchID');
        $programID = $request->input('programID'); // Program filter

        // Base query to get interviews with related program and batch information
        $query = Interview::with('program.batch');

        // Filter by batch if selected
        if ($batchID) {
            $query->where('batchID', $batchID); // Directly filter by batchID
        }

        // Filter by program if selected
        if ($programID) {
            $query->where('programID', $programID);
        }

        // Admin role sees all interviews and all programs
        if ($user->hasRole('admin')) {
            $interviews = $query->get(); // Fetch all interviews
            $programs = Program::all(); // Admins can see all programs
        }
        // Faculty role sees only their own programs' interviews
        else if ($user->hasRole('faculty')) {
            $query->whereHas('program', function ($query) use ($user) {
                $query->where('facultyID', $user->id);
            });
            $interviews = $query->get(); // Fetch faculty-specific interviews
            $programs = Program::where('facultyID', $user->id)->get(); // Fetch faculty-specific programs
        }

        // Fetch all batches
        $batches = Batch::all();

        // Return the view with filtered interviews, batches, and programs
        return view('interviews.index', compact('interviews', 'batches', 'programs'));
    }





    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        // Store the uploaded file temporarily
        $path = $request->file('file')->store('temp');

        // Read and parse the CSV data
        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));

        // Remove empty rows
        $rows = array_filter($rows, function ($row) {
            return !empty(array_filter($row)); // Ensure the row is not empty
        });

        // Get the headers and data rows
        $headers = array_shift($rows); // remove the first row (headers)

        // Pass the parsed data to a review view
        return view('interviews.review-csv', compact('rows', 'headers', 'path'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'programID' => 'required',
            'batchID' => 'required',
            'interviewees.*.intervieweeName' => 'required',
            'interviewees.*.contactNumber' => 'required',
        ]);

        // Loop through interviewees and insert them
        foreach ($request->interviewees as $interviewee) {
            Interview::create([
                'programID' => $request->programID,
                'batchID' => $request->batchID,
                'intervieweeName' => $interviewee['intervieweeName'],
                'contactNumber' => $interviewee['contactNumber'],
                'interviewStatus' => $interviewee['interviewStatus'] ?? 'Pending', // Set the interview status dynamically
            ]);
        }

        return redirect()->route('interviews.index')->with('success', 'Interviewees added successfully!');
    }

    public function bulkStoreCsv(Request $request)
    {
        // Retrieve the file path
        $path = $request->input('filePath');

        // Read the CSV file from storage
        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));

        // Remove the headers (first row)
        array_shift($rows);

        // Define the allowed interview status values (replace with your actual enum values)
        $validStatuses = ['Pending', 'Scheduled', 'Completed', 'Canceled'];

        // Loop through the data rows and insert them into the database
        foreach ($rows as $row) {
            // Check if the row contains enough columns and isn't empty
            if (count($row) >= 5 && !empty(array_filter($row))) {
                // Validate the batchID and programID combination exists in the programs table
                $programExists = Program::where('programID', $row[0])
                    ->where('batchID', $row[1])
                    ->exists();

                if (!$programExists) {
                    // Log invalid program and batch combination and skip the row
                    Log::warning('Invalid programID or batchID combination: ' . json_encode($row));
                    continue; // Skip this row
                }

                // Validate interviewStatus against the enum values
                if (in_array($row[4], $validStatuses)) {
                    Interview::create([
                        'programID' => $row[0],
                        'batchID' => $row[1],
                        'intervieweeName' => $row[2],
                        'contactNumber' => $row[3],
                        'interviewStatus' => $row[4],
                    ]);
                } else {
                    // Log invalid interview status and skip the row
                    Log::warning('Invalid interviewStatus: ' . json_encode($row));
                }
            }
        }

        // Redirect back to the index page
        return redirect()->route('interviews.index')->with('success', 'Interviewees added successfully!');
    }




    public function uploadCsv()
    {
        $programs = Program::all();
        $batches = Batch::all();

        return view('interviews.uploadCsv', compact('programs', 'batches'));
    }


    public function show($id)
    {
        // Fetch the interview by its ID
        $interview = Interview::findOrFail($id);

        // Return a view to display the interview
        return view('interviews.show', compact('interview'));
    }

    public function updateStatus(Request $request, $interviewID)
    {
        // Find the interview by ID
        $interview = Interview::findOrFail($interviewID);

        // Update the interview status
        $interview->interviewStatus = $request->input('interviewStatus');
        $interview->save();

        // Redirect back to the index with a success message
        return redirect()->route('interviews.index')->with('success', 'Interview status updated successfully.');
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
        $programs = Program::where('batchID', $batchID)->get();
        return response()->json($programs);
    }

    public function update(Request $request)
    {
        // Validate the input
        $request->validate([
            'interview_ids' => 'required|array',
            'newStatus' => 'required|in:Pending,Scheduled,Completed,Canceled',
        ]);

        // Get the selected interview IDs and new status
        $interviewIds = $request->input('interview_ids');
        $newStatus = $request->input('newStatus');

        // Update the interview statuses
        Interview::whereIn('interviewID', $interviewIds)->update([
            'interviewStatus' => $newStatus,
        ]);
        \Log::info('Interview IDs:', ['ids' => $interviewIds]);
        \Log::info('New Status:', ['status' => $newStatus]);



        // Redirect with success message
        return redirect()->route('interviews.index')->with('success', 'Selected interviews updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['interview_ids' => 'required|array']);

        Interview::whereIn('interviewID', $request->interview_ids)->delete();

        return redirect()->route('interviews.index')->with('success', 'Selected interviews deleted successfully.');
    }
}
