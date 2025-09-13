<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SmsOtpNotification;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-phone');
    }

    public function send(Request $request)
    {
        $user = $request->user();

        // Generate a 6-digit OTP valid for 10 minutes
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'phone_verification_code' => $code,
            'phone_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        // Send the OTP (currently just logs it, replace with SMS provider later)
        $user->notify(new SmsOtpNotification($code, $user->phone));

        return back()->with('status', 'otp-sent');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','digits:6'],
        ]);

        $user = $request->user();

        if (
            blank($user->phone_verification_code) ||
            now()->greaterThan($user->phone_verification_expires_at) ||
            $request->code !== $user->phone_verification_code
        ) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->markPhoneAsVerified();

        return redirect()->route('dashboard')->with('status', 'Phone Verified successfully,');
    }
}
