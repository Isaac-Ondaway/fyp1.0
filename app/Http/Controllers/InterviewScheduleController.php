<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Batch;
use App\Models\Program;
use App\Models\InterviewSchedule;
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
    
        // Query for events based on batch and program filters
        $query = InterviewSchedule::query()->with('interviewee', 'program', 'batch');
    
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
    
        // Fetch all batches and programs for filters
        $batches = Batch::all();
        $programs = Program::all();
    
        return view('interviews-schedule.index', compact('events', 'batches', 'programs','schedules'));
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
    public function calendarEvents()
    {
        $events = InterviewSchedule::with('interviewee')->get()->map(function ($schedule) {
            return [
                'title' => $schedule->interviewee->intervieweeName,
                'start' => $schedule->scheduled_date->format('Y-m-d H:i:s'),
                'extendedProps' => [
                    'status' => $schedule->status,
                    'remarks' => $schedule->remarks,
                ]
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

        // Fetch all schedules for the given date
        $schedules = InterviewSchedule::with('interviewee') // Relationship to `interviews` table
            ->whereDate('scheduled_date', $date)
            ->get();

        // Format the data to be sent to the frontend
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

        // Compose the email
        $emailService->sendEmail(
            $schedule->interviewee->email, // Access the email from the related `interviewee`
            'Interview Scheduled',
            "Dear {$schedule->interviewee->intervieweeName},\n\nYour interview is scheduled as follows:\nDate & Time: {$schedule->scheduled_date->format('Y-m-d H:i')}\n\nThank you!"
        );

            // Update the `status` column in the database
            $schedule->update(['status' => 'Scheduled']);

            // Log the update
            \Log::info("Interview schedule status updated to 'Scheduled' for interviewee_id: {$schedule->interviewee_id}");


        // Return success response
        return back()->with('success', "Email sent to {$schedule->interviewee->intervieweeName} successfully!");
    }
    

}
