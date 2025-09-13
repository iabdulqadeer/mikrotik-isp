<?php

namespace App\Support;

class Currency
{
    public static function forCountry(?string $country): array
    {
        $all = config('currency.by_country');
        return $all[$country ?? ''] ?? config('currency.default');
    }

    public static function money(float|int|null $amount, ?string $code, ?string $symbol): string
    {
        $amt = number_format((float)($amount ?? 0), 2, '.', ',');
        // Display like: "ETB 1,234.00" (consistent with your UI)
        return trim(sprintf('%s %s', $code ?: '', $amt));
    }
}
