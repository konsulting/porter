<?php
/**
 * Created by PhpStorm.
 * User: keoghan
 * Date: 01/08/2018
 * Time: 08:57
 */

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
                return new MacOs;

            case stristr($os, 'WIN'):
            case stristr($os, 'LINUX'):
            default :
                return new Untrained();
        }
    }
}
