<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\Artisan;

class Database
{
    /**
     * Create the database file if it doesn't exist.
     *
     * @param bool $forceRefresh
     * @return void
     */
    public static function ensureExists($forceRefresh = false)
    {
        if (! is_dir(static::databaseDirectory())) {
            mkdir(static::databaseDirectory(), 0777, true);
        }

        if (! static::exists()) {
            touch(static::databasePath());
            static::refresh();

            return;
        }

        if ($forceRefresh) {
            static::refresh();
        }
    }

    /**
     * Refresh the contents of the database.
     *
     * @return void
     */
    public static function refresh()
    {
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
    }

    /**
     * Check if the database file exists.
     *
     * @return bool
     */
    public static function exists()
    {
        return file_exists(static::databasePath());
    }

    /**
     * Get the database path.
     *
     * @return string
     */
    protected static function databasePath()
    {
        return config('database.connections.default.database');
    }

    /**
     * Get the database directory.
     *
     * @return string
     */
    protected static function databaseDirectory()
    {
        return dirname(static::databasePath());
    }
}
