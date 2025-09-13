<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\RedirectResponse;

class UtilityController extends Controller
{
    public function clearCache(): RedirectResponse
    {
        // Run cache/optimize clears (ignore output; failures will throw)
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        // Optional: if you want to show details, you can read Artisan::output() after each call

        return back()->with('ok', 'Application caches cleared successfully.');
    }
}
