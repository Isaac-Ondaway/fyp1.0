<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;

Route::middleware('web')->group(function () {
    Route::get('/google-calendar-events', [GoogleCalendarController::class, 'getEvents']);
    Route::post('/google-calendar-events', [GoogleCalendarController::class, 'createEvent']);
    Route::patch('/google-calendar-events/{id}', [GoogleCalendarController::class, 'updateEvent']);
    Route::delete('/google-calendar-events/{id}', [GoogleCalendarController::class, 'deleteEvent']);

});
