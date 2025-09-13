<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrialController extends Controller
{
    public function start(Request $request)
    {
        $user = $request->user();

        if (!$user->canStartTrial()) {
            return back()->with('error', 'You are not eligible to start a free trial.');
        }

        $user->trial_started_at = now();
        $user->trial_ends_at    = now()->addDays(3);
        if (isset($user->trial_used)) {
            $user->trial_used = true;
        }
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Your 3-day free trial is now active!');
    }
}
