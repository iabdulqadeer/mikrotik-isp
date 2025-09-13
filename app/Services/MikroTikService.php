<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
use Throwable;

class MikroTikService
{
    private function makeClient(Device $device): Client
    {
        $password = Crypt::decryptString($device->password_encrypted);
        $port = (int)($device->port ?? ($device->ssl ? 8729 : 8728));

        $config = [
            'host'           => $device->host,
            'user'           => $device->username,
            'pass'           => $password,
            'port'           => $port,
            'timeout'        => 5,  // connect timeout (s)
            'attempts'       => 1,
            'socket_timeout' => 5,  // read timeout (s)
            'ssl'            => (bool)$device->ssl, // true => TLS (usually 8729)
            'legacy'         => false,
        ];

        return new Client($config);
    }

    /** Returns list of interfaces (array). */
    public function printInterfaces(Device $device): array
    {
        $client = $this->makeClient($device);
        $query  = new Query('/interface/print');
        $resp   = $client->query($query)->read();

        return is_array($resp) ? $resp : [];
    }

    /** Connectivity test; returns ['ok'=>true,'latency_ms'=>int] or throws. */
    public function test(Device $device): array
    {
        $t0 = microtime(true);
        $this->printInterfaces($device);
        $latency = (int) round((microtime(true) - $t0) * 1000);

        return ['ok' => true, 'latency_ms' => $latency];
    }

    /** Friendly error mapper. */
    public function prettyMessage(Throwable $e): string
    {
        $m = $e->getMessage();

        $map = [
            'Authentication'           => 'Invalid username or password.',
            'invalid user name'        => 'Invalid username or password.',
            'cannot resolve'           => 'Host could not be resolved (check DNS/hostname).',
            'refused'                  => 'Connection refused. Is API enabled and port correct?',
            'Handshake'                => 'TLS handshake failed. Try toggling SSL or fix certificate.',
            'timed out'                => 'Connection timed out. Check reachability and firewall.',
            'No route to host'         => 'No route to host. Check network/IP.',
            'certificate'              => 'TLS certificate issue on the device.',
        ];

        foreach ($map as $needle => $nice) {
            if (stripos($m, $needle) !== false) return $nice;
        }

        return $m ?: 'Unknown error talking to RouterOS.';
    }
}
