<?php

use App\Models\Setting;

/**
 * Get a setting, or all settings.
 *
 * @param string|null $key
 * @param string|null $default
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
 * Check if a setting exists.
 *
 * @param string $key
 *
 * @return bool
 */
function setting_exists($key)
{
    return ! is_null(setting($key));
}

/**
 * Check if a setting is missing.
 *
 * @param string $key
 *
 * @return bool
 */
function setting_missing($key)
{
    return ! setting_exists($key);
}

/**
 * Check if we're running tests since environment is limited to production/development.
 */
function running_tests()
{
    return config('app.running_tests');
}
