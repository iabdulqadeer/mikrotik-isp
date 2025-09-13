<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

class SmsTemplate
{
    // Variables you showed in the UI hint
    public const VARS = [
        '@first_name', '@last_name', '@email', '@phone',
        '@package_name', '@expiry_at', '@account_number', '@paybill',
        '@till_number', '@password', '@username',
    ];

    public static function render(string $template, ?User $user): string
    {
        if (!$user) return $template;

        $map = [
            '@first_name'     => $user->first_name ?? Str::before($user->name ?? '', ' '),
            '@last_name'      => $user->last_name ?? Str::after($user->name ?? '', ' '),
            '@email'          => $user->email,
            '@phone'          => $user->phone ?? '',
            '@package_name'   => $user->package_name ?? '',
            '@expiry_at'      => optional($user->expiry_at)->format('M d, Y'),
            '@account_number' => $user->account_number ?? '',
            '@paybill'        => $user->paybill ?? '',
            '@till_number'    => $user->till_number ?? '',
            '@password'       => $user->plain_password ?? '', // only if you set it temporarily
            '@username'       => $user->username ?? $user->email,
        ];

        return str_replace(array_keys($map), array_values($map), $template);
    }
}
