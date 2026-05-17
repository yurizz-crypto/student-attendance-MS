<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        $user = Auth::user();

        // Check if user is active
        if ($user->status !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Your account is deactivated or suspended.']);
        }

        if ($user->role === 'student') {
            $cookieDeviceToken = $request->cookie('device_token');

            // If the user has no registered device, register this one
            if (empty($user->device_fingerprint)) {
                $newToken = (string) Str::uuid();
                $user->update(['device_fingerprint' => $newToken]);

                $request->session()->regenerate();

                return redirect()->intended(route('dashboard', absolute: false))
                    ->withCookie(cookie()->forever('device_token', $newToken));
            }
            // If they have a registered device, verify the cookie matches
            elseif ($user->device_fingerprint !== $cookieDeviceToken) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Unrecognized device. Please contact an administrator to reset your device binding.',
                ]);
            }
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
