<?php

namespace App\Providers;

use App\Support\DockerSync\EventSubscriber as DockerSyncSubscriber;
use App\Support\Mutagen\EventSubscriber as MutagenSubscriber;
use App\Support\Valet\EventSubscriber as ValetSubscriber;
use App\Support\XDebug\EventSubscriber as XdebugSubscriber;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        DockerSyncSubscriber::class,
        MutagenSubscriber::class,
        ValetSubscriber::class,
        XdebugSubscriber::class,
    ];
}
