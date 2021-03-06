<?php

namespace App\Support\Valet;

use App\Events\SiteRemoved;
use App\Events\SiteSecured;
use App\Events\SiteUnsecured;

class EventSubscriber
{
    /** @var Valet */
    protected $valet;

    public function __construct(Valet $valet)
    {
        $this->valet = $valet;
    }

    public function siteSecured(SiteSecured $event)
    {
        if (setting('use_valet', 'off') === 'off') {
            return;
        }

        $this->valet->addSite($event->site);
    }

    public function siteUnsecured(SiteUnsecured $event)
    {
        if (setting('use_valet', 'off') === 'off') {
            return;
        }

        $this->valet->addSite($event->site);
    }

    public function siteRemoved(SiteRemoved $event)
    {
        if (setting('use_valet', 'off') === 'off') {
            return;
        }

        $this->valet->removeSite($event->site);
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
        $events->listen(SiteSecured::class, static::class.'@siteSecured');
        $events->listen(SiteUnsecured::class, static::class.'@siteUnsecured');
        $events->listen(SiteRemoved::class, static::class.'@siteRemoved');
    }
}
