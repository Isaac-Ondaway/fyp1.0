<?php

// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Fetch all events
    public function fetchEvents()
    {
        $events = Event::all();
        $eventList = [];

        foreach ($events as $event) {
            $eventList[] = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime,
                'end' => $event->end_datetime,
                'color' => $event->color,
                'description' =>$event->description,
                'allDay' => $event->all_day == 1 ? true : false,
            ];
        }

        return response()->json($eventList);
    }

    // Store a new event
    public function storeEvent(Request $request)
    {
        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start_datetime = $request->start_datetime;
        $event->end_datetime = $request->end_datetime;
        $event->color = $request->color;
        $event->all_day = $request->all_day;
        $event->save();

        return response()->json(['success' => true]);
    }

    // Update an existing event
    public function updateEvent(Request $request, $id)
    {
        $event = Event::find($id);
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start_datetime = $request->start_datetime;
        $event->end_datetime = $request->end_datetime;
        $event->color = $request->color;
        $event->all_day = $request->all_day;
        $event->save();

        return response()->json(['success' => true]);
    }

    // Delete an event
    public function deleteEvent($id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json(['success' => true]);
    }
}

