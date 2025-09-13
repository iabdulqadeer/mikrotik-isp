<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\Impersonation;

class EnsureTwoFactorIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ✅ If admin is impersonating, bypass 2FA gate
        if (Impersonation::isActive()) {
            return $next($request);
        }

        // Only enforce for role "user"
        if ($user?->hasRole('user')) {
            if ($user->two_factor_enabled && ! session('two_factor_passed')) {
                if (! $request->routeIs('2fa.*')) {
                    return redirect()->route('2fa.challenge');
                }
            }
        }

        return $next($request);
    }
}
