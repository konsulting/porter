<?php

namespace App\Support\Mechanics;

class ChooseMechanic
{
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

        switch (true) {
            case (bool) stristr($os, 'DAR'):
                return app(MacOs::class);

            case (bool) stristr($os, 'WIN'):
                return app(Windows::class);

            case (bool) stristr($os, 'LINUX'):
                return app(Linux::class);

            default:
                return app(Untrained::class);
        }
    }
}
