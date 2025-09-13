<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\Impersonation;

class EnsureEmailAndPhoneIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // ✅ If admin is impersonating, bypass verification gates
        if (Impersonation::isActive()) {
            return $next($request);
        }

        // ✅ Only enforce checks if the user has "user" role
        if ($user->hasRole('user')) {
            if (is_null($user->email_verified_at)) {
                return redirect()->route('verification.notice');
            }
            if (is_null($user->phone_verified_at)) {
                return redirect()->route('phone.verify.notice');
            }
        }

        return $next($request);
    }
}
