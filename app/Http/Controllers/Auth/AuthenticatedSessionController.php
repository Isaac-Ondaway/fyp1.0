<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Corrected the redirect syntax for intended route
        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        if ($user) {
            // Clear Google tokens from the user's database record
            $user->google_access_token = null;
            $user->google_refresh_token = null;
            $user->google_token_expires_at = null;
            $user->google_email = null;
            $user->save();
        }

        // Clear any Google token stored in the session
        $request->session()->forget('google_access_token');

        // Log out the user
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
