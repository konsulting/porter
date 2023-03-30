<?php

namespace App\Support\Images;

use Illuminate\Support\Str;

class Image
{
    /**
     * Image constructor.
     *
     * @param $name
     * @param string $name
     */
    public function __construct(protected $name, protected ?string $localPath = null)
    {
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
     */
    public function getLocalPath(): ?string
    {
        return $this->localPath;
    }
}
