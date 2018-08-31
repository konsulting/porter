<?php

namespace App\Ssl\Trust;

use App\Ssl\Trust\Mechanics\MacOs;
use App\Ssl\Trust\Mechanics\Untrained;

class ChooseMechanic
{
    public static function forOS($os = null)
    {
        $os = $os ?: PHP_OS;

        switch (true) {
            case stristr($os, 'DAR'):
                return app(MacOs::class);

            case stristr($os, 'WIN'):
            case stristr($os, 'LINUX'):
            default :
                return app(Untrained::class);
        }
    }
}
