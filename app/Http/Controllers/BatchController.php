<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    // Display a listing of batches
    public function index()
    {
        $batches = Batch::orderBy('batchStartDate', 'desc')->get();
        return view('batches.index', compact('batches'));
    }

    // Show the form for creating a new batch
    public function create()
    {
        return view('batches.create');
    }

    // Store a newly created batch in the database
    public function store(Request $request)
    {
        $request->validate([
            'batchID' => 'required|unique:batches,batchID|regex:/^[0-9]+$/', // Example for numeric batchID
            'batchName' => 'required|string|max:255',
            'batchStartDate' => 'required|date',
        ]);

        Batch::create([
            'batchID' => $request->batchID,
            'batchName' => $request->batchName,
            'batchStartDate' => $request->batchStartDate,
        ]);

        return redirect()->route('batches.index')->with('success', 'Batch created successfully.');
    }

    // Show the form for editing a batch
    public function edit($id)
    {
        $batch = Batch::findOrFail($id);
        return view('batches.edit', compact('batch'));
    }

    // Update the specified batch in the database
    public function update(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);
    
        // Validation rules
        $request->validate([
            'batchID' => 'required|string|unique:batches,batchID,' . $batch->batchID . ',batchID', // Allow current batchID
            'batchName' => 'required|string|max:255',
            'batchStartDate' => 'required|date',
        ]);
    
        // Update the batch
        $batch->update([
            'batchID' => $request->batchID, // Include batchID if you want it to be updatable
            'batchName' => $request->batchName,
            'batchStartDate' => $request->batchStartDate,
        ]);
    
        return redirect()->route('batches.index')->with('success', 'Batch updated successfully.');
    }
    

    // Remove the specified batch from the database
    public function destroy($id)
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();

        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
    }
}
