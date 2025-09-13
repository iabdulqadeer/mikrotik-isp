<?php

// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware aliases
        $middleware->alias([
            // Spatie Permission (use the singular 'Middleware' namespace)
            'role'                => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'          => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'  => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            // Your custom "active user" gate
            'active'              => \App\Http\Middleware\EnsureUserIsActive::class,
            'email_phone_verified' => \App\Http\Middleware\EnsureEmailAndPhoneIsVerified::class,

              // ⬇️ Add this line
            'subscribed.or.trial'  => \App\Http\Middleware\EnsureSubscribedOrOnTrial::class,

            '2fa' => \App\Http\Middleware\EnsureTwoFactorIsVerified::class,    

        ]);

        // Allow Stripe (Cashier) webhooks to bypass CSRF
        // Cashier registers POST /stripe/webhook by default
        $middleware->validateCsrfTokens(except: ['stripe/*','webhooks/twilio/status']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // Optional: register providers here (only if you actually need them)
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,
        // \App\Providers\AppServiceProvider::class,
        // \App\Providers\EventServiceProvider::class,
        // \App\Providers\RouteServiceProvider::class,
    ])
    ->create();
