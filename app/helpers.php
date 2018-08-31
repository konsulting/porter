<?php

use App\Setting;

/**
 * Get a setting, or all settings
 *
 * @param null $key
 * @param null $default
 *
 * @return mixed
 */
function setting($key = null, $default = null)
{
    if (!$key) {
        return Setting::all()->pluck('value', 'name');
    }

    return Setting::where('name', $key)->value('value') ?? $default;
}
