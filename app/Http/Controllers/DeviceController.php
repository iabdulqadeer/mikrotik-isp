<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MikroTikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;  // add this import


class DeviceController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:devices.view')->only(['index','show']);
        $this->middleware('permission:devices.create')->only(['create','store']);
        $this->middleware('permission:devices.edit')->only(['edit','update']);
        $this->middleware('permission:devices.delete')->only(['destroy']);
        $this->middleware('permission:devices.test')->only(['test']);
        $this->middleware('permission:devices.provision')->only(['provisionLink']);
    }

    /**
     * List devices with search & status filters (used by the new UI).
     */
    public function index(Request $request)
    {
        $q      = trim((string) $request->get('q'));
        $status = $request->get('status'); // null|online|offline

        $devices = Device::query()
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('host', 'like', "%{$q}%")
                      ->orWhere('identity', 'like', "%{$q}%");
                });
            })
            ->when($status === 'online', function ($qb) {
                $qb->whereNotNull('last_seen_at')->where('last_seen_at', '>', now()->subMinutes(5));
            })
            ->when($status === 'offline', function ($qb) {
                $qb->where(function ($w) {
                    $w->whereNull('last_seen_at')->orWhere('last_seen_at', '<=', now()->subMinutes(5));
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a new device.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'host'     => 'required|string|max:255',
            'port'     => 'nullable|integer',
            'ssl'      => 'nullable|boolean',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'identity' => 'nullable|string|max:255',
        ]);

        // Persist encrypted password and normalized flags
        $data['password_encrypted'] = Crypt::encryptString($data['password']);
        unset($data['password']);

        $data['port']        = (int) ($data['port'] ?? 8728);
        $data['ssl']         = (bool) ($data['ssl'] ?? false);
        $data['created_by']  = $request->user()?->id;
        $data['provision_token'] = Str::random(40);

        $device = Device::create($data);

        return redirect()
            ->route('devices.show', $device)
            ->with('ok', 'Device added.');
    }

    public function show(Device $device)
    {
        return view('devices.show', compact('device'));
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    /**
     * Update device.
     */
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'host'     => 'required|string|max:255',
            'port'     => 'nullable|integer',
            'ssl'      => 'nullable|boolean',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'identity' => 'nullable|string|max:255',
        ]);

        if (!empty($data['password'])) {
            $data['password_encrypted'] = Crypt::encryptString($data['password']);
        }
        unset($data['password']);

        $data['port'] = (int) ($data['port'] ?? 8728);
        $data['ssl']  = (bool) ($data['ssl'] ?? false);

        // Keep/repair token if missing
        if (empty($device->provision_token)) {
            $data['provision_token'] = Str::random(40);
        }

        $device->update($data);

        return redirect()
            ->route('devices.show', $device)
            ->with('ok', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()
            ->route('devices.index')
            ->with('ok', 'Device removed.');
    }

    public function test(Request $request, Device $device, MikroTikService $svc)
{
    try {
        $result = $svc->test($device);

        $device->forceFill(['last_seen_at' => now()])->save();

        Log::info('MikroTik device test OK', [
            'device_id' => $device->id,
            'host'      => $device->host,
            'port'      => $device->port ?? ($device->ssl ? 8729 : 8728),
            'ssl'       => (bool)$device->ssl,
            'latency'   => $result['latency_ms'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status'       => 'ok',
                'message'      => 'Connected',
                'latency_ms'   => $result['latency_ms'],
                'last_seen_at' => $device->last_seen_at?->toDateTimeString(),
            ]);
        }

        return back()->with('ok', 'Connected in '.$result['latency_ms'].' ms');
    } catch (\Throwable $e) {
        $msg = $svc->prettyMessage($e);

        Log::error('MikroTik device test FAILED', [
            'device_id' => $device->id,
            'host'      => $device->host,
            'port'      => $device->port ?? ($device->ssl ? 8729 : 8728),
            'ssl'       => (bool)$device->ssl,
            'error'     => $e->getMessage(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'error', 'message' => $msg], 422);
        }

        return back()->with('error', 'Connect failed: '.$msg);
    }
}

/** Show the provision command with a device-bound token. */
public function provisionLink(Device $device)
{
    if (empty($device->provision_token)) {
        $device->forceFill(['provision_token' => \Illuminate\Support\Str::random(40)])->save();
    }

    // Build absolute URL with ?token=...
    $url = route('provision.rsc', ['token' => $device->provision_token], true);

    return view('devices.provision', compact('device', 'url'));
}
}
