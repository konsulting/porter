<?php

namespace App\Support\DockerSync;

use App\Events\BuiltDockerCompose;
use App\Events\StartingPorter;
use App\Events\StartingPorterService;
use App\Events\StoppingPorter;
use App\Events\StoppingPorterService;
use App\Support\Console\ConsoleWriter;

class EventSubscriber
{
    /** @var DockerSync */
    protected $dockerSync;

    public function __construct(DockerSync $dockerSync)
    {
        $this->dockerSync = $dockerSync;
    }

    public function startAll(StartingPorter $event)
    {
        $this->dockerSync->startDaemon();
    }

    public function startForService(StartingPorterService $event)
    {
        $this->dockerSync->startDaemon();
    }

    public function stopAll(StoppingPorter $event)
    {
        $this->dockerSync->stopDaemon();
    }

    public function stopForService(StoppingPorterService $event)
    {
        $this->dockerSync->stopDaemon();
    }

    public function adaptDockerCompose(BuiltDockerCompose $event)
    {
        if ($this->dockerSync->isActive()) {
            app(ConsoleWriter::class)->info('Adapting DockerCompose YAML for DockerSync');
            $this->dockerSync->adjustDockerComposeSetup($event->filePath);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     *
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(StartingPorter::class, static::class.'@startAll');
        $events->listen(StartingPorterService::class, static::class.'@startForService');
        $events->listen(StoppingPorter::class, static::class.'@stopAll');
        $events->listen(StoppingPorterService::class, static::class.'@stopForService');
        $events->listen(BuiltDockerCompose::class, static::class.'@adaptDockerCompose');
    }
}
