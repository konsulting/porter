<?php

namespace App\Events;

class StartedPorterService
{
    public $service;

    public function __construct(string $service)
    {
        $this->service = $service;
    }
}
