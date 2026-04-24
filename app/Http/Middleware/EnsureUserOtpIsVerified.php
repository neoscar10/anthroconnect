<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOtpIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not logged in, standard auth middleware will handle it, 
        // but just in case we are called before it.
        if (! $user) {
            return $next($request);
        }

        // Only enforce for regular users (assuming admins have a different check or bypass)
        // If admins also use this, we might need a bypass check.
        // For now, let's follow the prompt: "Only frontend user auth should switch to WhatsApp phone."
        if ($user->user_type !== 'admin' && ! $user->otp_verified_at) {
            if (! $request->routeIs('user.otp.verify')) {
                return redirect()->route('user.otp.verify');
            }
        }

        return $next($request);
    }
}
