<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Storage;

class GoogleCalendarService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope([
            Google_Service_Calendar::CALENDAR,
            "https://www.googleapis.com/auth/userinfo.email",
            "https://www.googleapis.com/auth/userinfo.profile"
        ]);

        // Load and set the access token
        $token = $this->loadAccessToken();
        if ($token) {
            $this->setAccessToken($token);
        }
    }

    /**
     * Generate the Google OAuth authorization URL
     *
     * @return string
     */
    public function createAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle the authorization code and fetch the access token
     *
     * @param string $code
     * @return array|null
     */
    public function fetchAccessTokenWithAuthCode($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Set the access token on the client
     *
     * @param array $token
     */
    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);

        // Optionally store the token for later use
        if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken()) {
            $this->refreshAccessToken();
        }
    }

    /**
     * Refresh the access token if expired and store it
     */
    public function refreshAccessToken()
    {
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $this->storeAccessToken($this->client->getAccessToken());
        }
    }

    /**
     * Store the access token (for example, in the session or file)
     *
     * @param array $token
     */
    public function storeAccessToken($token)
    {
        session(['google_access_token' => $token]);

        // Alternatively, you could store it in a file (not recommended for production)
        if (env('GOOGLE_TOKEN_PATH')) {
            Storage::put(env('GOOGLE_TOKEN_PATH'), json_encode($token));
        }
    }

    /**
     * Create a new event in Google Calendar
     *
     * @param array $data
     * @param string $calendarId
     * @return Google_Service_Calendar_Event
     */
    public function createEvent(array $eventData)
    {
        \Log::info('Starting event creation process');
    
        // Check if user and access token exist
        $user = Auth::user();
        \Log::info('Authenticated User:', ['user' => $user]);
    
        if (!$user || !$user->google_access_token) {
            \Log::error('Google access token not found or user not authenticated');
            return response()->json(['error' => 'Google access token not found or user not authenticated'], 401);
        }
    
        // Set up Google Client
        $accessToken = json_decode($user->google_access_token, true);
        $this->client->setAccessToken($accessToken);
    
        // Check if the token is expired and refresh if necessary
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $user->google_refresh_token;
            if ($refreshToken) {
                $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                $this->client->setAccessToken($newAccessToken);
                $user->google_access_token = json_encode($newAccessToken);
                $user->google_token_expires_at = now()->addSeconds($newAccessToken['expires_in']);
                $user->save();
                \Log::info('Access token refreshed successfully');
            } else {
                \Log::error('No refresh token available');
                return response()->json(['error' => 'No refresh token available'], 403);
            }
        }
    
        try {
            // Set up Google Calendar Service
            $service = new \Google_Service_Calendar($this->client);
            $calendarId = 'primary';
    
            // Validate and format event data
            $startDateTime = $eventData['start_datetime'] ?? null;
            $endDateTime = $eventData['end_datetime'] ?? null;
    
            if (!$startDateTime || !$endDateTime) {
                \Log::error('Start or end datetime missing in request.');
                return response()->json(['error' => 'Start or end datetime is missing'], 422);
            }
    
            // Create a new Google Calendar event
            $event = new \Google_Service_Calendar_Event([
                'summary' => $eventData['title'],
                'description' => $eventData['description'] ?? '',
                'start' => [
                    'dateTime' => $this->formatToIso8601($eventData['start_datetime']),
                    'timeZone' => 'Asia/Kuala_Lumpur',
                ],
                'end' => [
                    'dateTime' => $this->formatToIso8601($eventData['end_datetime']),
                    'timeZone' => 'Asia/Kuala_Lumpur',
                ],
            ]);
    
            // Insert the event into Google Calendar
            $createdEvent = $service->events->insert($calendarId, $event);
    
            // Log the response and check for ID
            \Log::info('Google Calendar API Response:', ['response' => $createdEvent]);
    
            if (!isset($createdEvent->id)) {
                \Log::error('Google Calendar API did not return an event ID.', ['response' => $createdEvent]);
                return response()->json(['error' => 'Failed to create event: Google API did not return an ID'], 500);
            }
    
            \Log::info('Event created successfully with ID: ' . $createdEvent->id);

            return [
                'id' => $createdEvent->id,
                'summary' => $createdEvent->getSummary(),
                'start' => $createdEvent->getStart(),
                'end' => $createdEvent->getEnd(),
            ];

        } catch (\Google_Service_Exception $e) {
            \Log::error('Google API error: ' . $e->getMessage());
            return response()->json(['error' => 'Google Calendar API error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            \Log::error('General error: ' . $e->getMessage());
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }
    
    

    /**
     * Update an existing event in Google Calendar
     *
     * @param string $eventId
     * @param array $data
     * @param string $calendarId
     * @return Google_Service_Calendar_Event
     */
    public function updateEvent(string $eventId, array $data, $calendarId = 'primary')
    {
        $service = new Google_Service_Calendar($this->client);

        $event = $service->events->get($calendarId, $eventId);
        $event->setSummary($data['title']);
        $event->setDescription($data['description'] ?? '');
        $event->setStart(['dateTime' => $data['start_datetime'], 'timeZone' => 'UTC']);
        $event->setEnd(['dateTime' => $data['end_datetime'], 'timeZone' => 'UTC']);

        return $service->events->update($calendarId, $eventId, $event);
    }
    
    
    /**
     * Delete an event from Google Calendar
     *
     * @param string $eventId
     * @param string $calendarId
     */
    public function deleteEvent(string $eventId, $calendarId = 'primary')
    {
        $service = new Google_Service_Calendar($this->client);
        $service->events->delete($calendarId, $eventId);
    }

    /**
     * Retrieve events from the Google Calendar
     *
     * @param string $calendarId
     * @return array
     */
    public function getEvents($calendarId = 'primary')
    {
        try {
            $service = new Google_Service_Calendar($this->client);
            $events = $service->events->listEvents($calendarId);
    
            $eventList = [];
            foreach ($events->getItems() as $event) {
                $eventList[] = [
                    'id' => $event->getId(),
                    'summary' => $event->getSummary(),
                    'start' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                    'end' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                ];
            }
    
            return $eventList;
        } catch (\Exception $e) {
            // Log the error and handle gracefully
            logger()->error('Google Calendar API Error: ' . $e->getMessage());
            return []; // Return empty array to prevent breaking the flow
        }
    }
    

    /**
     * Load the access token from storage (e.g., session or file)
     *
     * @return array|null
     */
    public function loadAccessToken()
    {
        $token = session('google_access_token');
        if (!$token && env('GOOGLE_TOKEN_PATH') && Storage::exists(env('GOOGLE_TOKEN_PATH'))) {
            $token = json_decode(Storage::get(env('GOOGLE_TOKEN_PATH')), true);
            session(['google_access_token' => $token]); // Store it back in the session
        }
        return $token;
    }


    private function formatToIso8601($dateTime)
{
    $date = new \DateTime($dateTime);
    return $date->format('Y-m-d\TH:i:s');
}

    
}
