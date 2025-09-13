<?php

namespace App\Models\Concerns;

use App\Models\UserSetting;
use Illuminate\Support\Facades\Cache;

trait HasUserSettings
{
    public function settingGet(string $key, mixed $default = null): mixed
    {
        return Cache::remember("user:{$this->id}:setting:$key", 60, function () use ($key, $default) {
            $row = UserSetting::where('user_id', $this->id)->where('key', $key)->first();
            if (!$row) return $default;
            $decoded = json_decode($row->value, true);
            return $decoded === null && $row->value !== 'null' ? $row->value : $decoded;
        });
    }

    public function settingPut(string $key, mixed $value): void
    {
        Cache::forget("user:{$this->id}:setting:$key");
        $payload = is_string($value) ? $value : json_encode($value);
        UserSetting::updateOrCreate(
            ['user_id' => $this->id, 'key' => $key],
            ['value' => $payload],
        );
    }

    public function settingPutMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) $this->settingPut($k, $v);
    }

    public function settingMany(array $keys, array $defaults = []): array
    {
        return collect($keys)->mapWithKeys(fn($k) => [$k => $this->settingGet($k, $defaults[$k] ?? null)])->all();
    }
}
