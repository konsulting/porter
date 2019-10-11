<?php

namespace App\Support\Images;

use Illuminate\Support\Str;

class Image
{
    /** @var string */
    protected $name;

    /** @var string|null */
    protected $localPath;

    /**
     * Image constructor.
     *
     * @param $name
     * @param string|null $localPath
     */
    public function __construct($name, $localPath = null)
    {
        $this->name = $name;
        $this->localPath = $localPath;
    }

    /**
     * Return the name for this image.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the un-versioned name for this image.
     *
     * @return string
     */
    public function getUnVersionedName()
    {
        return Str::before($this->name, ':');
    }

    /**
     * Return the local path for the image, if it is local.
     *
     * @return string|null
     */
    public function getLocalPath()
    {
        return $this->localPath;
    }
}
