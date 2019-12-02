<?php

namespace App\Events;

class StoppingPorterService
{
    public $service;

    public function __construct(string $service)
    {
        $this->service = $service;
    }
}
