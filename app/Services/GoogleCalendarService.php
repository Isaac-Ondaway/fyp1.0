<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
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
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
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
     * Retrieve events from the Google Calendar
     *
     * @param string $calendarId
     * @return array
     */
    public function getEvents($calendarId = 'primary')
    {
        $service = new Google_Service_Calendar($this->client);
        $events = $service->events->listEvents($calendarId);

        $eventList = [];
        foreach ($events->getItems() as $event) {
            $eventList[] = [
                'summary' => $event->getSummary(),
                'start' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                'end' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
            ];
        }

        return $eventList;
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
}
