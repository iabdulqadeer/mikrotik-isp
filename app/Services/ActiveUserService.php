<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RouterOS\Client;
use RouterOS\Query;
use Throwable;

class ActiveUserService
{
    public function get(?string $type = null, ?string $q = null): array
    {
        $cacheKey = 'active-sessions:'.md5(json_encode([$type, $q]));
        return Cache::remember($cacheKey, now()->addSeconds(15), function () use ($type, $q) {

            $devices = Device::query()->get();
            $hotspot = collect();
            $pppoe   = collect();

            foreach ($devices as $d) {
                try {
                    $client = $this->makeClient($d);
                    if (!$client) continue;

                    if ($type === null || $type === 'hotspot') {
                        $hotspot = $hotspot->merge($this->fetchHotspot($client, $d->name ?? $d->host));
                    }
                    if ($type === null || $type === 'pppoe') {
                        $pppoe = $pppoe->merge($this->fetchPPPoE($client, $d->name ?? $d->host));
                    }
                } catch (Throwable $e) {
                    report($e);
                    continue;
                }
            }

            $all = $hotspot->merge($pppoe);

            if ($q) {
                $all = $all->filter(function ($row) use ($q) {
                    $hay = strtolower(
                        ($row['username'] ?? '').' '.
                        ($row['ip'] ?? '').' '.
                        ($row['mac'] ?? '').' '.
                        ($row['router'] ?? '')
                    );
                    return str_contains($hay, strtolower($q));
                })->values();
            }

            $items = match ($type) {
                'hotspot' => $all->where('type','hotspot')->values(),
                'pppoe'   => $all->where('type','pppoe')->values(),
                default   => $all->values(),
            };

            return [
                'items'  => $items,
                'counts' => [
                    'all'     => $hotspot->count() + $pppoe->count(),
                    'hotspot' => $hotspot->count(),
                    'pppoe'   => $pppoe->count(),
                ]
            ];
        });
    }

    protected function makeClient($device): ?Client
    {
        try {
            return new Client([
                'host'     => $device->host,
                'user'     => $device->username,
                'pass'     => decrypt($device->password), // adjust if not encrypted
                'port'     => (int)($device->port ?? 8728),
                'timeout'  => 5,
                'attempts' => 1,
                'ssl'      => (bool)($device->ssl ?? false),
                'legacy'   => false,
            ]);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    protected function fetchHotspot(Client $client, string $routerName): Collection
    {
        try {
            $rows = collect($client->query(new Query('/ip/hotspot/active/print'))->read());
        } catch (Throwable $e) {
            return collect();
        }

        return $rows->map(fn($r) => [
            'type'         => 'hotspot',
            'username'     => $r['user'] ?? null,
            'ip'           => $r['address'] ?? null,
            'mac'          => $r['mac-address'] ?? null,
            'router'       => $routerName,
            'session_start'=> $this->fromUptime($r['uptime'] ?? null),
            'session_end'  => null,
            'raw'          => $r,
        ]);
    }

    protected function fetchPPPoE(Client $client, string $routerName): Collection
    {
        try {
            $rows = collect($client->query(new Query('/ppp/active/print'))->read());
        } catch (Throwable $e) {
            return collect();
        }

        return $rows->map(fn($r) => [
            'type'         => 'pppoe',
            'username'     => $r['name'] ?? null,
            'ip'           => $r['address'] ?? null,
            'mac'          => $r['caller-id'] ?? null,
            'router'       => $routerName,
            'session_start'=> $this->fromUptime($r['uptime'] ?? null),
            'session_end'  => null,
            'raw'          => $r,
        ]);
    }

    protected function fromUptime(?string $uptime)
    {
        if (!$uptime) return null;
        $s = 0;
        if (preg_match_all('/(\d+)([dhms])/', $uptime, $m, PREG_SET_ORDER)) {
            foreach ($m as $p) {
                $n = (int)$p[1];
                $u = $p[2];
                $s += match($u){'d'=>$n*86400,'h'=>$n*3600,'m'=>$n*60,'s'=>$n,default=>0};
            }
        }
        return now()->subSeconds($s);
    }
}
