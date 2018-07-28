<?php

use App\Setting;

/**
 * Get a setting, or all settings
 *
 * @param null $key
 * @return mixed
 */
function setting($key = null) {
    if (! $key) {
        return Setting::all();
    }

    return Setting::where('name', $key)->first();
}

/**
 * Prepare docker-compose string for command, including pointer to YAML file
 * @param null $command
 * @return string
 */
function docker_compose($command = null)
{
    return 'docker-compose -f '.base_path('docker-compose.yaml').($command ? ' '.$command : '');
}


/**
 * Work out the site name from the current working dir
 * Only if we are in the home directory or below.
 *
 * @return null|string
 */
function site_from_cwd()
{
    $currentPath = getcwd();
    $home = setting('home');

    if (! str_start($currentPath, $home)) {
        return null;
    }

    $currentPath = trim(str_after($currentPath, $home), DIRECTORY_SEPARATOR);

    return str_before($currentPath, DIRECTORY_SEPARATOR);
}
