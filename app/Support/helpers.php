<?php

use App\Models\Setting;

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
    if (! $key) {
        return Setting::all()->pluck('value', 'name');
    }

    return Setting::where('name', $key)->value('value') ?? $default;
}

/**
 * Determine the appropriate path for the library of user preference
 *
 * @return bool|string
 */
function determineLibraryPath()
{
    return $_SERVER['HOME'] . '/.porter';
}
