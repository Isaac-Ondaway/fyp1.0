<?php

// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class EventController extends Controller
{

    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }


    // Fetch all events
    public function fetchEvents()
    {
        try {
            // Fetch public events from the local database
            $publicEvents = Event::where('visibility', 'public')->get();

    
            $eventList = [];
    
            // Add local events
            foreach ($publicEvents as $event) {
                $eventList[] = [
                    'id' => 'local-' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start_datetime,
                    'end' => $event->end_datetime,
                    'color' => $event->color ?? '#176dde',
                    'description' => $event->description,
                    'allDay' => $event->all_day == 1,
                ];
            }
    
            // Attempt to fetch Google Calendar events only if the user is authenticated
            if ($this->isGoogleAuthenticated()) {
                try {
                    $googleEvents = $this->googleCalendarService->getEvents();
    
                    foreach ($googleEvents as $googleEvent) {
                        $eventList[] = [
                            'id' => 'google-' . $googleEvent['id'],
                            'title' => $googleEvent['summary'],
                            'start' => $googleEvent['start'],
                            'end' => $googleEvent['end'],
                            'color' => '#f39c12',
                            'description' => $googleEvent['description'] ?? '',
                            'allDay' => false,
                        ];
                    }
                } catch (\Exception $e) {
                    // Log the Google API error but don't fail the request
                    logger()->error('Google Calendar API Error:', ['error' => $e->getMessage()]);
                }
            } else {
                logger()->info('User has not authenticated with Google Calendar.');
            }
    
            return response()->json($eventList);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }
    }
    
    /**
     * Check if the user has a valid Google OAuth token
     */
    private function isGoogleAuthenticated()
    {
        // This assumes you are storing the Google token in the session or database
        $googleToken = session('google_token'); // Adjust based on how you store tokens
    
        return !empty($googleToken);
    }
    
    

    // Store a new event
    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'color' => 'nullable|string',
            'visibility' => 'required|in:public,private',
            'all_day' => 'required|boolean',
        ]);
    
        // Initialize Google Event ID
        $googleEventId = null;
    
        // Logic for Private Events: Save in Google Calendar only
        if ($request->visibility === 'private') {
            $googleEvent = $this->googleCalendarService->createEvent($request->all());
        
            if ($googleEvent && isset($googleEvent['id'])) {
                $googleEventId = $googleEvent['id'];
                \Log::info('Private event saved in Google Calendar', ['event_id' => $googleEventId]);
        
                return response()->json([
                    'success' => true,
                    'message' => 'Private event saved in Google Calendar',
                    'google_event_id' => $googleEventId,
                ]);
            } else {
                return response()->json(['error' => 'Failed to save private event in Google Calendar'], 500);
            }
        }
        
        // Logic for Public Events: Save in Local Database only
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'color' => $request->color,
            'visibility' => $request->visibility,
            'all_day' => $request->all_day,
        ]);
    
        \Log::info('Public event saved in local database', ['event_id' => $event->id]);
    
        return response()->json([
            'success' => true,
            'message' => 'Public event saved in local database',
            'event' => $event,
        ]);
    }
    
    

    public function updateEvent(Request $request, $id)
    {
        $event = Event::find($id);
    
        $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'visibility' => 'required|in:public,private',
            'color' => 'nullable|string',
        ]);
    
        // Update event details in Google Calendar if it's public
        if ($event->visibility === 'public') {
            $this->googleCalendarService->updateEvent($event->google_event_id, $request->all());
        }
    
        // Update event details in the database
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'color' => $request->color,
            'all_day' => $request->all_day ?? false,
            'visibility' => $request->visibility,
        ]);
    
        return response()->json(['success' => true, 'event' => $event]);
    }
    

    public function deleteEvent($id)
    {
        $event = Event::find($id);
    
        // Delete event from Google Calendar if it's public
        if ($event->visibility === 'public') {
            $this->googleCalendarService->deleteEvent($event->google_event_id);
        }
    
        // Delete the event from the database
        $event->delete();
    
        return response()->json(['success' => true]);
    }
    
    
}

