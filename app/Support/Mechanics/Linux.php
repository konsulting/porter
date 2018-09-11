<?php

namespace App\Support\Mechanics;

use App\Support\Console\ServerBag;

class Linux extends Untrained
{
    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return app(ServerBag::class)->get('HOME');
    }
}
