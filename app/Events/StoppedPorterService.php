<?php


namespace App\Events;


class StoppedPorterService
{
    public $service;

    public function __construct(string $service)
    {
        $this->service = $service;
    }
}
