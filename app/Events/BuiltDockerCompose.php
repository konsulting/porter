<?php

namespace App\Events;

class BuiltDockerCompose
{
    /** @var string */
    public $filePath;

    /**
     * BuiltDockerCompose constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }
}
