<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Batch;
use App\Models\Program;
use App\Models\InterviewSchedule;
use Carbon\Carbon;
use App\Services\SystemEmailService;
use Illuminate\Http\Request;

class InterviewScheduleController extends Controller
{
    /**
     * Display a listing of the scheduled interviews.
     */
    public function index(Request $request)
    {
        $batchID = $request->input('batchID');
        $programID = $request->input('programID');
    
        // Get the logged-in user's faculty ID
        $facultyID = auth()->user()->facultyID;
    
        // Query for events based on batch, program filters, and faculty ownership
        $query = InterviewSchedule::query()->with('interviewee', 'program', 'batch')
            ->whereHas('program', function ($query) use ($facultyID) {
                $query->where('facultyID', $facultyID);
            });
    
        if ($batchID) {
            $query->where('batch_id', $batchID);
        }
        if ($programID) {
            $query->where('program_id', $programID);
        }
    
        $schedules = $query->get();
    
        // Format schedules for FullCalendar
        $events = $schedules->map(function ($schedule) {
            return [
                'title' => $schedule->interviewee->intervieweeName,
                'start' => $schedule->scheduled_date,
                'program' => $schedule->program->programName,
            ];
        });
    
        // Fetch all batches and programs for filters (limited to the logged-in user's faculty)
        $batches = Batch::all();
        $programs = Program::where('facultyID', $facultyID)->get();
    
        return view('interviews-schedule.index', compact('events', 'batches', 'programs', 'schedules'));
    }
    
    
    /**
     * Show the form for creating a new interview schedule.
     *
     * @param int $interviewee_id
     */
    public function create($interviewee_id)
    {
        $interview = Interview::with('program', 'program.batch')->findOrFail($interviewee_id);
        
        // Retrieve program and batch from the interviewee's program relationship
        $program = $interview->program;
        $batch = $program->batch ?? null;
    
        return view('interviews-schedule.create', compact('interview', 'program', 'batch'));
    }
    

    /**
     * Store a newly created interview schedule in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'interviewee_id' => 'required|exists:interviews,interviewID',
            'program_id' => 'required|string',
            'batch_id' => 'required|integer',
            'scheduled_date'   => 'required|date_format:Y-m-d H:i',
            'remarks' => 'nullable|string',
            'venue' => 'required|string',
            'status' => 'required|in:Pending,Scheduled,Attended,Absent',
        ]);
    
        // Find existing schedule for the interviewee and date, or create a new one
        InterviewSchedule::updateOrCreate(
            [
                'interviewee_id' => $request->interviewee_id
            ],
            [
                'program_id' => $request->program_id,
                'batch_id' => $request->batch_id,
                'scheduled_date' => $request->scheduled_date,
                'remarks' => $request->remarks,
                'status' => $request->status ?? 'Scheduled',
                'venue' =>$request->venue,
            ]
        );
    
        return redirect()->route('interviews-schedule.index')->with('success', 'Interview schedule saved successfully!');
    }
    

    /**
     * Get interview schedules as JSON for the calendar.
     */
    public function calendarEvents(Request $request)
    {
        $query = InterviewSchedule::with('interviewee', 'program');
    
        if (!auth()->user()->hasRole('Admin')) {
            // If not admin, filter by faculty
            $facultyID = auth()->user()->facultyID;
            $query->whereHas('program', function ($query) use ($facultyID) {
                $query->where('facultyID', $facultyID);
            });
        }
    
        $events = $query->get()->map(function ($schedule) {
            return [
                'title' => $schedule->interviewee->intervieweeName,
                'start' => $schedule->scheduled_date->format('Y-m-d H:i:s'),
                'extendedProps' => [
                    'status' => $schedule->status,
                    'remarks' => $schedule->remarks,
                ],
            ];
        });
    
        return response()->json($events);
    }
    
    
    /**
     * Show the form for editing the specified interview schedule.
     *
     * @param int $id
     */
    public function edit($id)
    {
        $schedule = InterviewSchedule::with('interviewee')->findOrFail($id);
        return view('interviews-schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified interview schedule in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
            'status' => 'required|in:Pending,Scheduled,Attended,Absent,Accepted,Rejected',
            'venue' => 'required|string',
        ]);

        $schedule = InterviewSchedule::findOrFail($id);
        $schedule->update([
            'remarks' => $request->input('remarks'),
            'status' => $request->input('status'),
            'venue' => $request->input('venue'),
        ]);

        return redirect()->route('interviews-schedule.index')->with('success', 'Interview schedule updated successfully');
    }

    /**
     * Remove the specified interview schedule from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        $schedule = InterviewSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('interviews-schedule.index')->with('success', 'Interview schedule deleted successfully');
    }

    public function getEventsForDate(Request $request)
    {
        $date = $request->input('date');
        $query = InterviewSchedule::with('interviewee', 'program');
    
        if (!auth()->user()->hasRole('Admin')) {
            // If not admin, filter by faculty
            $facultyID = auth()->user()->facultyID;
            $query->whereHas('program', function ($query) use ($facultyID) {
                $query->where('facultyID', $facultyID);
            });
        }
    
        $schedules = $query->whereDate('scheduled_date', $date)->get();
    
        $events = $schedules->map(function ($schedule) {
            return [
                'schedule_id' => $schedule->schedule_id,
                'title' => $schedule->interviewee->intervieweeName,
                'time' => $schedule->scheduled_date->format('H:i'),
                'scheduled_date' => $schedule->scheduled_date->format('Y-m-d H:i'),
                'interviewee_id' => $schedule->interviewee_id,
                'status' => $schedule->status,
                'remarks' => $schedule->remarks,
                'email' => $schedule->interviewee->email,
                'venue' => $schedule->venue,
                'contactNumber' => $schedule->interviewee->contactNumber,
                'programName' => $schedule->program->programName ?? 'N/A', // Add program name
                'batchName' => $schedule->batch->batchName ?? 'N/A', // Add batch name
            ];
        });
    
        return response()->json($events);
    }
    
    


    public function scheduleInterview(Request $request, SystemEmailService $emailService)
{
    // Log the request to verify it's received
    \Log::info('Email Request Received:', $request->all());

    // Validate the input
    $validated = $request->validate([
        'interviewee_id' => 'required|exists:interviews,interviewID', // Validate against `interviews.interviewID`
        'scheduled_date' => 'required|date_format:Y-m-d H:i',
    ]);

    // Retrieve the schedule from `interview_schedule`
    $schedule = InterviewSchedule::with('interviewee')->where('interviewee_id', $validated['interviewee_id'])->firstOrFail();

    // Retrieve venue (assuming `venue` is a column in the `interview_schedule` table)
    $venue = $schedule->venue ?? 'To be confirmed'; // Fallback if venue is null

     // Format the date and time
     $formattedDate = Carbon::parse($schedule->scheduled_date)->format('j M Y h:iA'); // Example: 9 Jan 2025 08:00AM

    // Compose the email
    $emailService->sendEmail(
        $schedule->interviewee->email, // Access the email from the related `interviewee`
        'Interview Invitation: Scheduled Details',
        "Dear {$schedule->interviewee->intervieweeName},\n\n" .
        "We are pleased to invite you to attend an interview for the program you have applied to. Below are the details of your scheduled interview:\n\n" .
        "Date & Time: {$formattedDate} \n" .
        "Venue: {$venue} \n\n" .
        "Please ensure that you arrive at least 15 minutes prior to your scheduled time and bring all necessary documents as communicated earlier.\n\n" .
        "If you have any questions or require further assistance, please do not hesitate to contact us.\n\n" .
        "Best regards,\n\n" .
        "The Admissions Team\n" .
        "UMS - FPOMS"
    );

    // Update the `status` column in the database
    $schedule->update(['status' => 'Scheduled']);

    // Log the email sent
    \Log::info('Interview scheduled email sent to:', ['email' => $schedule->interviewee->email]);

    return back()->with('success', "Email sent to {$schedule->interviewee->intervieweeName} successfully!");
}

}