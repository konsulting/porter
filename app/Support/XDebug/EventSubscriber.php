<?php

namespace App\Support\XDebug;

use App\Events\StartedPorter;
use App\Events\StartedPorterService;

class EventSubscriber
{
    /** @var XDebug */
    protected $xdebug;

    public function __construct(XDebug $xdebug)
    {
        $this->xdebug = $xdebug;
    }

    public function setXDebug($event)
    {
        if (($event->service ?? false) && strpos('php_fpm', $event->service) === false) {
            return;
        }

        if (setting('use_xdebug', 'on') === 'off') {
            $this->xdebug->turnOff();
        }

        $this->xdebug->turnOn();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     *
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(StartedPorter::class, static::class.'@setXDebug');
        $events->listen(StartedPorterService::class, static::class.'@setXDebug');
    }
}
