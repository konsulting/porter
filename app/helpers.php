<?php

use App\Setting;
use App\Support\ConsoleWriter;

/**
 * Get a setting, or all settings
 *
 * @param null $key
 * @return mixed
 */
function setting($key = null)
{
    if (!$key) {
        return Setting::all()->pluck('value', 'name');
    }

    return optional(Setting::where('name', $key)->first())->value;
}

/**
 * Prepare docker-compose string for command, including pointer to YAML file
 * @param null $command
 * @return string
 */
function docker_compose($command = null)
{
    return 'docker-compose -f ' . config('app.docker-compose-file') . ($command ? ' ' . $command : '');
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

    if (!str_start($currentPath, $home)) {
        return null;
    }

    $currentPath = trim(str_after($currentPath, $home), DIRECTORY_SEPARATOR);

    return str_before($currentPath, DIRECTORY_SEPARATOR);
}

/**
 * Return the console writer for use outside commands. Optionally output an info message;
 *
 * @param null $msg
 * @param null $level
 * @return \Illuminate\Foundation\Application|mixed
 */
function console_writer($msg = null, $level = null)
{
    $writer = app(ConsoleWriter::class);

    if ($msg) {
        if (! in_array($level, ['alert', 'warn', 'error', 'info'])) {
            $level = 'info';
        }

        $writer->{$level}($msg);
    }

    return $writer;
}
