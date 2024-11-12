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
            'batchName' => 'required|string|max:255',
            'batchStartDate' => 'required|date',
        ]);

        Batch::create($request->only(['batchName', 'batchStartDate']));

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
        $request->validate([
            'batchName' => 'required|string|max:255',
            'batchStartDate' => 'required|date',
        ]);

        $batch = Batch::findOrFail($id);
        $batch->update($request->only(['batchName', 'batchStartDate']));

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
