<?php

namespace App\Support\Mechanics;

class ChooseMechanic
{
    const MECHANICS = [
        'DAR'   => MacOs::class,
        'WIN'   => Windows::class,
        'LINUX' => Linux::class,
    ];

    /**
     * Get the mechanic for the host operating system.
     *
     * @param string|null $os
     *
     * @return Mechanic
     */
    public static function forOS($os = null)
    {
        $os = $os ?: PHP_OS;

        foreach (static::MECHANICS as $key => $class) {
            if (stristr($os, $key)) {
                return app($class);
            }
        }

        return app(Untrained::class);
    }
}
