<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // If no OTP exists or it has expired, generate a new one
        if (! $user->otp_code || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            $this->generateAndSendOtp($user);
        }

        return view('auth.otp');
    }

    public function store(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'The OTP has expired. Please request a new one.']);
        }

        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'The provided OTP is incorrect.']);
        }

        // OTP is correct, mark as verified in session
        $request->session()->put('otp_verified_'.$user->id, true);

        // Clear the OTP from DB
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Redirect to the main dashboard
        return redirect()->route('dashboard');
    }

    public function resend(Request $request)
    {
        $this->generateAndSendOtp($request->user());

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    protected function generateAndSendOtp($user)
    {
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new SendOtpNotification($otp));
    }
}
