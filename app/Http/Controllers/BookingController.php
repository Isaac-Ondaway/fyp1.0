<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Resource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $oneMonthFromNow = Carbon::now()->addMonth();
    
        if (auth()->user()->hasRole('admin')) {
            $upcomingBookings = Booking::with(['resource', 'student'])
                ->where('status', 'Confirmed')
                ->where('start_time', '<=', $oneMonthFromNow)
                ->where('start_time', '>=', Carbon::now()) // Future events only
                ->orderBy('start_time')
                ->get();
    
            $pendingBookings = Booking::with(['resource', 'student'])
                ->where('status', 'Pending')
                ->orderBy('start_time')
                ->get();
    
            $confirmedBookings = Booking::with(['resource', 'student'])
                ->where('status', 'Confirmed')
                ->orderBy('start_time')
                ->get();
    
            $cancelledBookings = Booking::with(['resource', 'student'])
                ->where('status', 'Cancelled')
                ->orderBy('start_time')
                ->get();
        } else {
            $upcomingBookings = Booking::with(['resource', 'student'])
                ->where('studentID', auth()->id())
                ->where('status', 'Confirmed')
                ->where('start_time', '<=', $oneMonthFromNow)
                ->where('start_time', '>=', Carbon::now()) // Future events only
                ->orderBy('start_time')
                ->get();
    
            $pendingBookings = Booking::with(['resource', 'student'])
                ->where('studentID', auth()->id())
                ->where('status', 'Pending')
                ->orderBy('start_time')
                ->get();
    
            $confirmedBookings = Booking::with(['resource', 'student'])
                ->where('studentID', auth()->id())
                ->where('status', 'Confirmed')
                ->orderBy('start_time')
                ->get();
    
            $cancelledBookings = Booking::with(['resource', 'student'])
                ->where('studentID', auth()->id())
                ->where('status', 'Cancelled')
                ->orderBy('start_time')
                ->get();
        }
    
        return view('bookings.index', compact('upcomingBookings', 'pendingBookings', 'confirmedBookings', 'cancelledBookings'));
    }
    
    
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $resources = Resource::all(); // Fetch all available resources
        return view('bookings.create', compact('resources'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info($request->all());

        $validated = $request->validate([
            'resourceID' => 'required|exists:resources,resourceID',
            'programName' => 'required|string|max:255',
            'phoneNo' => 'required|string|max:255',
            'numberOfParticipant' => 'required|integer|min:1',
            'matricNo' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);
    
        // Add the studentID to the data to be saved
        $validated['studentID'] = auth()->id();
    
        Booking::create($validated);
    
        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        return view('resources.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        $this->authorize('update', $resource);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        $resource->update($request->all());

        return redirect()->route('resources.index')->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
