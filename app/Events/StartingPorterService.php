<?php

namespace App\Events;

class StartingPorterService
{
    public $service;

    public function __construct(string $service)
    {
        $this->service = $service;
    }
}
