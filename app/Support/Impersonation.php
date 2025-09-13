<?php

namespace App\Support;

class Impersonation
{
    public const KEY = 'impersonator_id';

    public static function start(int $impersonatorId): void
    {
        session([self::KEY => $impersonatorId]);
    }

    public static function stop(): void
    {
        session()->forget(self::KEY);
    }

    public static function isActive(): bool
    {
        return (bool) session(self::KEY);
    }

    public static function impersonatorId(): ?int
    {
        return session(self::KEY);
    }
}
