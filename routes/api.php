<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeartbeatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are stateless and use the "api" middleware group.
| Good for webhooks / device call-backs like the MikroTik heartbeat.
*/

Route::post('/device/heartbeat', HeartbeatController::class)
    ->name('api.device.heartbeat');
