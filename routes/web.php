<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\IntakesController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegisterTokenController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Inertia\Inertia;

// Home Route
Route::get('/', function () {
    return view('welcome');
});

//User Manage
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [RegisterTokenController::class, 'listUsers'])->name('admin.user.list');
    Route::post('/admin/generate-standalone-invite', [RegisterTokenController::class, 'generateStandaloneToken'])->name('admin.generate.standaloneInvite');
    Route::get('/admin/users/{id}/edit', [RegisterTokenController::class, 'edit'])->name('admin.editUser');
    Route::put('/admin/users/{id}', [RegisterTokenController::class, 'update'])->name('admin.updateUser');
    Route::delete('/admin/users/{id}', [RegisterTokenController::class, 'destroy'])->name('admin.deleteUser');
});

//Registration Route
Route::get('/register/{token}', [RegisterTokenController::class, 'showRegistrationForm'])->name('register.token');
Route::post('/register/{token}', [RegisterTokenController::class, 'registerUser'])->name('register.user');

 

// Dashboard Route - Accessible only to authenticated and verified users
// Dashboard Route
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


// Profile Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Program Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/batch/{batchID}', [ProgramController::class, 'getProgramsByBatch']);
    Route::get('/programs/{programID}/{batchID}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
    Route::patch('/programs/{programID}/{batchID}', [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{programID}/{batchID}', [ProgramController::class, 'destroy'])->name('programs.destroy');
    Route::get('/programs/manage-entry-levels', [ProgramController::class, 'manageEntryLevels'])->name('programs.manage_entry_levels');
    Route::post('/programs/manage-entry-levels', [ProgramController::class, 'updateEntryLevels'])->name('programs.update_entry_levels');

});


// Route to store a new intake
Route::post('/intakes', [IntakesController::class, 'store'])->name('intakes.store');
Route::get('/intakes', [IntakesController::class, 'index'])->name('intakes.index');
Route::post('/intakes/storeAll', [IntakesController::class, 'storeAll'])->name('intakes.storeAll');
// Include the routes for authentication (login, register, etc.)
require __DIR__.'/auth.php';


// Custom Interview Routes
Route::get('/interviews', [InterviewController::class, 'index'])
    ->name('interviews.index')
    ->middleware('auth'); // Only include if authentication is required

Route::get('/interviews/upload-csv', [InterviewController::class, 'uploadCsv'])->name('interviews.uploadCsv');
Route::post('/interviews/bulk-upload', [InterviewController::class, 'bulkUpload'])->name('interviews.bulkUpload');
Route::post('/interviews/bulk-store', [InterviewController::class, 'bulkStore'])->name('interviews.bulkStore');
Route::post('/interviews/bulk-store-csv', [InterviewController::class, 'bulkStoreCsv'])->name('interviews.bulkStoreCsv');
Route::put('/interviews/{interview}/update-status', [InterviewController::class, 'updateStatus'])->name('interviews.updateStatus');
Route::post('/interviews/bulk-update-ajax', [InterviewController::class, 'bulkUpdateAjax'])->name('interviews.bulkUpdateAjax');
// Batch and Program Fetching Routes
Route::get('/interviews/get-batches/{programID}', [InterviewController::class, 'getBatchesForProgram']);
Route::get('/interviews/get-programs-for-batch/{batchID}', [InterviewController::class, 'getProgramsForBatch']);
// Resource Route (defined at the end to avoid conflicts)
Route::resource('interviews', InterviewController::class)->except(['store']);
Route::get('/interviews/{id}/edit', [InterviewController::class, 'edit'])->name('interviews.edit');
Route::put('/interviews/{id}', [InterviewController::class, 'update'])->name('interviews.update');
// Route for displaying the bulk edit page
Route::get('/interviews/edit', [InterviewController::class, 'bulkEdit'])->name('interviews.bulkEdit');
// Route for processing the bulk update
Route::put('/interviews/bulk-update', [InterviewController::class, 'bulkUpdate'])
    ->middleware('auth')  // Ensure this is correctly applied
    ->name('interviews.bulkUpdate');
Route::delete('/interviews/bulk-delete', [InterviewController::class, 'bulkDelete'])->name('interviews.bulkDelete');

//Bookings Route
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', BookingController::class);
});
Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');


// //Event Route
Route::get('/events/fetch', [EventController::class, 'fetchEvents']);
Route::post('/events/store', [EventController::class, 'storeEvent']);
Route::post('/events/update/{id}', [EventController::class, 'updateEvent']);
Route::post('/events/delete/{id}', [EventController::class, 'deleteEvent']);

// //Google Calendar
Route::get('/auth/google-calendar', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');

//Batches Route
Route::resource('batches', BatchController::class);

//Interview Scheduling 
Route::get('/interviews-schedule', [InterviewScheduleController::class, 'index'])->name('interviews-schedule.index');
Route::get('/interviews-schedule/create/{interviewee_id}', [InterviewScheduleController::class, 'create'])->name('interviews-schedule.create');
Route::post('/interviews-schedule', [InterviewScheduleController::class, 'store'])->name('interviews-schedule.store');
Route::get('/interviews-schedule/calendar-events', [InterviewScheduleController::class, 'calendarEvents']);
Route::get('/interviews-schedule/{id}/edit', [InterviewScheduleController::class, 'edit'])->name('interviews-schedule.edit');
Route::put('/interviews-schedule/{id}', [InterviewScheduleController::class, 'update'])->name('interviews-schedule.update');
Route::delete('/interviews-schedule/{id}', [InterviewScheduleController::class, 'destroy'])->name('interviews-schedule.destroy');
Route::get('/interviews-schedule/events-for-date', [InterviewScheduleController::class, 'getEventsForDate']);

Route::post('/send-email', [InterviewScheduleController::class, 'scheduleInterview'])->name('interview.schedule');

//Faculty Route

Route::resource('faculty', FacultyController::class);

//Report Route
Route::get('/reports/combined', [ReportController::class, 'combinedReport'])->name('reports.combined');


// Chat Routes for Authenticated Users
Route::middleware(['auth'])->group(function () {
    // Shared routes for both admin and faculty
    Route::get('/chats/{sessionId}/messages', [ChatSessionController::class, 'fetchMessages'])->name('chats.fetchMessages');
    Route::post('/chats/{sessionId}/message', [ChatSessionController::class, 'sendMessage'])->name('chats.sendMessage');
    
    // Admin-specific routes
    Route::prefix('admin')->group(function () {
        Route::get('/chats', [ChatSessionController::class, 'adminIndex'])->name('admin.chats.index');
        Route::get('/chats/{sessionId}/messages', [ChatSessionController::class, 'getAdminMessages'])->name('admin.chats.messages');
        Route::post('/chats/{sessionId}/message', [ChatSessionController::class, 'sendAdminMessage'])->name('admin.chats.sendMessage');
    });

    // Fetch session ID for chat
    Route::get('/chat/session-id', [ChatSessionController::class, 'getSessionId'])->name('chat.getSessionId');
});

Route::middleware(['auth'])->group(function () {
    Broadcast::routes(); // Register broadcasting routes
    require base_path('routes/channels.php'); // Load the channels file
});
