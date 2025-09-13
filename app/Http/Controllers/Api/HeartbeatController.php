<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class HeartbeatController extends Controller
{
    public function __invoke(Request $r)
    {
        $token = $r->query('token');
        $d = Device::where('provision_token', $token)->firstOrFail();
        $d->update([
            'last_seen_at' => now(),
            'identity'     => $r->input('identity'),
        ]);
        return response()->json(['ok' => true]);
    }
}
