<?php

use App\Models\Setting;

/**
 * Get a setting, or all settings.
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

/**
 * Check if we're running tests since environment is limited to production/development.
 */
function running_tests()
{
    return config('app.running_tests');
}
