<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of the faculty.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $faculties = Faculty::all(); // Fetch all faculties
        return view('faculty.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('faculty.create');
    }

    /**
     * Store a newly created faculty in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faculty,name',
        ]);

        Faculty::create([
            'name' => $request->name,
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty created successfully.');
    }

    /**
     * Show the form for editing the specified faculty.
     *
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\View\View
     */
    public function edit(Faculty $faculty)
    {
        return view('faculty.edit', compact('faculty'));
    }

    /**
     * Update the specified faculty in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faculty,name,' . $faculty->id,
        ]);

        $faculty->update([
            'name' => $request->name,
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty updated successfully.');
    }

    /**
     * Remove the specified faculty from storage.
     *
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Faculty $faculty)
    {
        $faculty->delete();

        return redirect()->route('faculty.index')->with('success', 'Faculty deleted successfully.');
    }
}
