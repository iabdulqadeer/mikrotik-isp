<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribedOrOnTrial
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ($user->hasActiveSubscription() || $user->isOnTrial())) {
            return $next($request);
        }

        // Block and send to subscription page
        return redirect()
            ->route('subscriptions.index') // adjust to your route name
            ->with('error', 'Your free trial has ended. Please choose a plan to continue.');
    }
}
