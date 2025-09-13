<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Impersonation;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! auth()->user()->is_active) {
            // If we're impersonating and target becomes inactive, return the admin back safely
            if (Impersonation::isActive()) {
                $adminId = Impersonation::impersonatorId();
                Impersonation::stop();
                Auth::logout();

                if ($adminId) {
                    Auth::loginUsingId($adminId);
                    return redirect()
                        ->route('users.index')
                        ->with('err', 'Impersonation ended: user account is deactivated.');
                }

                return redirect()->route('login')->with('err', 'Session ended: user account is deactivated.');
            }

            // Normal user became inactive
            auth()->logout();
            return redirect()
                ->route('login')
                ->with('err', 'Your account is deactivated. Contact support.');
        }

        return $next($request);
    }
}
