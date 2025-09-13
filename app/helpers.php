<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('usetting')) {
    function usetting(string $key, mixed $default = null) {
        $u = auth()->user();
        return $u ? $u->settingGet($key, $default) : $default;
    }

}