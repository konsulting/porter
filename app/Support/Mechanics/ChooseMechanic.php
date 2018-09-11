<?php

namespace App\Support\Mechanics;

use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Untrained;
use App\Support\Mechanics\Windows;

class ChooseMechanic
{
    public static function forOS($os = null)
    {
        $os = $os ?: PHP_OS;

        switch (true) {
            case stristr($os, 'DAR'):
                return app(MacOs::class);

            case stristr($os, 'WIN'):
                return app(Windows::class);

            case stristr($os, 'LINUX'):
                return app(Linux::class);

            default :
                return app(Untrained::class);
        }
    }
}
