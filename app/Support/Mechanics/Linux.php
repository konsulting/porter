<?php

namespace App\Support\Mechanics;

class Linux extends Untrained
{
    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return $this->serverBag->get('HOME');
    }
}
