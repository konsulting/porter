<?php

namespace App\Support\Mechanics;

use App\Support\Console\ServerBag;

class Windows extends Untrained
{
    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        $bag = app(ServerBag::class);

        return $bag->get('HOME') ?? $bag->get('HOMEDRIVE').$bag->get('HOMEPATH');
    }
}
