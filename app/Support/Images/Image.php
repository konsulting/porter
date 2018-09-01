<?php

namespace App\Support\Images;

class Image
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $localPath;

    public function __construct($name, $localPath = null)
    {
        $this->name = $name;
        $this->localPath = $localPath;
    }

    public function __get($name)
    {
        return $this->{$name} ?? null;
    }
}
