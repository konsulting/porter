<?php

namespace App\Support\Mechanics;

class ChooseMechanic
{
    final public const MECHANICS = [
        'DAR'   => MacOs::class,
        'WIN'   => Windows::class,
        'LINUX' => Linux::class,
    ];

    /**
     * Get the mechanic for the host operating system.
     *
     *
     * @return Mechanic
     */
    public static function forOS(?string $os = null)
    {
        $os = $os ?: PHP_OS;

        foreach (static::MECHANICS as $key => $class) {
            if (stristr($os, (string) $key)) {
                return app()->make($class);
            }
        }

        return app()->make(Untrained::class);
    }
}
