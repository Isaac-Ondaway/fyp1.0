<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\RegistrationToken;
use Carbon\Carbon;

class ValidateRegistrationToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $token = $request->route('token');
    
        $registrationToken = RegistrationToken::where('token', $token)
                        ->where('expires_at', '>', Carbon::now())
                        ->where('used', false)
                        ->first();
    
        if (!$registrationToken) {
            return redirect('/')->withErrors('Invalid or expired registration link.');
        }
    
        return $next($request);
    }
}
