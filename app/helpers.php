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
    return stristr(__DIR__, '.composer/')
        ? realpath(__DIR__.'/../../../../.porter') // global required - so, home dir
        : realpath(__DIR__.'/../../.porter'); // in another dir
}
