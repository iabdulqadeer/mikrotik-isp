<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key','value'];
    public $timestamps = true;

    /** Get a setting, json-decoded */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:$key", 60, function () use ($key, $default) {
            $row = static::query()->where('key', $key)->first();
            if (!$row) return $default;
            $decoded = json_decode($row->value, true);
            return $decoded === null && $row->value !== 'null' ? $row->value : $decoded;
        });
    }

    /** Set (stores JSON for anything non-string) */
    public static function put(string $key, mixed $value): void
    {
        Cache::forget("setting:$key");
        $payload = is_string($value) ? $value : json_encode($value);
        static::query()->updateOrCreate(['key' => $key], ['value' => $payload]);
    }

    /** Bulk helpers */
    public static function putMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) static::put($k, $v);
    }

    public static function getMany(array $keys, array $defaults = []): array
    {
        return collect($keys)->mapWithKeys(fn($k) => [$k => static::get($k, $defaults[$k] ?? null)])->all();
    }
}
