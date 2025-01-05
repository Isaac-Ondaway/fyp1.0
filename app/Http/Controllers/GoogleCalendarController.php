<?php

namespace App\Http\Controllers;

use Google_Client;
use Google\Service\Calendar;
use Google\Client;
use Google_Service_Calendar;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GoogleCalendarController extends Controller
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
    }
    

    public function redirectToGoogle()
    {
        $user = Auth::user();
    
        // Check if the user already has a valid access token
        if ($user->google_access_token && $user->google_token_expires_at > now()) {
            return redirect('/dashboard')->with('success', 'Already connected to Google Calendar');
        }
    
        // Use the existing client initialized in the constructor
        $this->client->setAccessType('offline'); // Ensure offline access for refresh tokens
        $this->client->setPrompt('select_account consent'); // Re-prompt user to select account
    
        // Generate the authorization URL
        $authUrl = $this->client->createAuthUrl();
    
        // Redirect the user to Google for authorization
        return redirect($authUrl);
    }
    
    
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            try {
                // Exchange the authorization code for an access token
                $accessToken = $this->client->fetchAccessTokenWithAuthCode($request->get('code'));

    
                // Check if there's an error in the access token response
                if (isset($accessToken['error'])) {
                    throw new \Exception($accessToken['error_description']);
                }
    
                // Set the access token in the client
                $this->client->setAccessToken($accessToken);
    
                // Verify token is not expired, refresh if necessary
                if ($this->client->isAccessTokenExpired()) {
                    $accessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $this->client->setAccessToken($accessToken);
                    \Log::info('Access token refreshed.');
                }
    
                // Fetch user information using the access token
                try {
                    $oauth2 = new \Google_Service_Oauth2($this->client);
                    $googleUser = $oauth2->userinfo->get();

                } catch (\Exception $e) {
                    \Log::error('Failed to fetch Google user info: ' . $e->getMessage());
                    return redirect('/dashboard')->with('error', 'Failed to fetch Google user info.');
                }
    
                // Store the access token and Google email in the user’s record
                $user = Auth::user();
                $user->google_access_token = json_encode($accessToken);
                $user->google_refresh_token = $accessToken['refresh_token'] ?? $user->google_refresh_token;
                $user->google_token_expires_at = now()->addSeconds($accessToken['expires_in']);
                $user->google_email = $googleUser->email;
                $user->save();
    
    
                // Logging after save to confirm
                if ($user->wasChanged()) {
                    \Log::info('Google token saved successfully.');
                } else {
                    \Log::error('Failed to save Google token.');
                }
    
                return redirect('/dashboard')->with('success', 'Google Calendar access granted');
            } catch (\Exception $e) {
                \Log::error('Error during Google callback: ' . $e->getMessage());
                return redirect('/dashboard')->with('error', 'Failed to authenticate with Google: ' . $e->getMessage());
            }
        }
    
        return redirect('/dashboard')->with('error', 'Failed to authenticate with Google');
    }
    

    public function getEvents()
    {
        try {
            $user = Auth::user();
            $accessToken = json_decode($user->google_access_token, true);

            if (!$accessToken) {
                return response()->json(['error' => 'Access token not found'], 401);
            }
    
            // Set the access token in the client
            $this->client->setAccessToken($accessToken);
    
            // Check if the token is expired and refresh it if needed
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $user->google_refresh_token;
                if ($refreshToken) {
                    $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $this->client->setAccessToken($newAccessToken);
    
                    // Update the user's access token and expiry
                    $user->google_access_token = json_encode($newAccessToken);
                    $user->google_token_expires_at = now()->addSeconds($newAccessToken['expires_in']);
                    $user->save();
                } else {
                    return response()->json(['error' => 'Unable to refresh access token'], 401);
                }
            }
    
            $service = new Google_Service_Calendar($this->client);
            $calendarId = 'primary';
    
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
    
            return response()->json($eventList);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch events: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch events from Google Calendar'], 500);
        }
    }



    public function updateEvent(Request $request, $id)
    {
        \Log::info('Attempting minimal update for event ID: ' . $id);
    
        // Get the authenticated user and their Google access token
        $user = Auth::user();
        $accessToken = json_decode($user->google_access_token, true);
    
        // Set the access token for the Google client
        $this->client->setAccessToken($accessToken);
    
        // Check if the token is expired and refresh it if necessary
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $newAccessToken = $this->client->getAccessToken();
            
            // Save the refreshed access token to the user's record
            $user->google_access_token = json_encode($newAccessToken);
            $user->save();
            
            \Log::info('Access token was refreshed and saved to the user profile.');
        }
    
        $service = new Google_Service_Calendar($this->client);
        $calendarId = 'primary';
    
        try {
            $event = $service->events->get($calendarId, $id);
    
            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime($request->input('start'));
            $event->setStart($startDateTime);
    
            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endDateTime->setDateTime($request->input('end'));
            $event->setEnd($endDateTime);
    
            $updatedEvent = $service->events->update($calendarId, $id, $event);
            \Log::info("Event updated successfully: " . json_encode($updatedEvent));
            return response()->json(['success' => true, 'event' => $updatedEvent]);
        } catch (Google_Service_Exception $e) {
            // Log Google-specific API errors
            \Log::error("Google API error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update event due to Google API error'], $e->getCode());
        } catch (\Exception $e) {
            // Log general errors
            \Log::error("General error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update event due to an unexpected error'], 500);
        }
    }
    
    
    // public function deleteEvent($id)
    // {
    //     \Log::info('Attempting to delete event with ID: ' . $id);
    
    //     $user = Auth::user(); // Get the authenticated user
    //     $accessToken = json_decode($user->google_access_token, true);
    
    //     // Set the access token for the Google client
    //     $this->client->setAccessToken($accessToken);
    
    //     // Check if the token is expired and refresh it if necessary
    //     if ($this->client->isAccessTokenExpired()) {
    //         $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
    //         $newAccessToken = $this->client->getAccessToken();
            
    //         // Save the refreshed access token to the user's record
    //         $user->google_access_token = json_encode($newAccessToken);
    //         $user->save();
    //     }
    
    //     try {
    //         $service = new Google_Service_Calendar($this->client);
    //         $calendarId = 'primary';
            
    //         // Attempt to delete the event
    //         $service->events->delete($calendarId, $id);
    //         \Log::info('Event deleted successfully.');
    
    //         return response()->json(['success' => true]);
    //     } catch (Google_Service_Exception $e) {
    //         \Log::error("Google API error while deleting event: " . $e->getMessage());
    //         return response()->json(['error' => 'Failed to delete event: ' . $e->getMessage()], $e->getCode());
    //     } catch (\Exception $e) {
    //         \Log::error("General error while deleting event: " . $e->getMessage());
    //         return response()->json(['error' => 'Failed to delete event'], 500);
    //     }
    // }
    public function deleteEvent($id)
    {
        try {
            $client = new Google_Client();
            $client->setAccessToken(auth()->user()->google_access_token); // Replace with your token logic
    
            $service = new Google_Service_Calendar($client);
            $service->events->delete('primary', $id); // 'primary' is the default calendar
    
            return response()->json(['message' => 'Event deleted successfully'], 200);
        } catch (\Google_Service_Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    


    
}
