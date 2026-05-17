<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->session()->get('otp_verified_'.$request->user()->id)) {
            // Check if the current route is already the OTP verification route to prevent infinite loops
            if (! $request->routeIs('otp.verify') && ! $request->routeIs('otp.store') && ! $request->routeIs('otp.resend') && ! $request->routeIs('logout')) {
                return redirect()->route('otp.verify');
            }
        }

        return $next($request);
    }
}
