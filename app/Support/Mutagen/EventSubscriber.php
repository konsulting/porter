<?php


namespace App\Support\Mutagen;


use App\Events\StartedPorter;
use App\Events\StoppedPorter;
use App\Events\BuiltDockerCompose;
use App\Events\StartedPorterService;
use App\Events\StoppedPorterService;
use App\Support\Console\ConsoleWriter;

class EventSubscriber
{
    /** @var Mutagen */
    protected $mutagen;

    public function __construct(Mutagen $mutagen)
    {
        $this->mutagen = $mutagen;
    }

    public function startAll(StartedPorter $event)
    {
        $this->mutagen->startDaemon();
        $this->mutagen->syncVolumes();
    }

    public function startForService(StartedPorterService $event)
    {
        $this->mutagen->startDaemon();
        $this->mutagen->syncVolumes();
    }

    public function stopAll(StoppedPorter $event)
    {
        $this->mutagen->stopDaemon();
    }

    public function stopForService(StoppedPorterService $event)
    {
        $this->mutagen->stopDaemon();
    }

    public function adaptDockerCompose(BuiltDockerCompose $event)
    {
        if ($this->mutagen->isActive()) {
            app(ConsoleWriter::class)->info("Adapted DockerCompose YAML for Mutagen");
            $this->mutagen->removeVolumesFromDockerCompose($event->filePath);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(StartedPorter::class, static::class."@startAll");
        $events->listen(StartedPorterService::class, static::class."@startForService");
        $events->listen(StoppedPorter::class, static::class."@stopAll");
        $events->listen(StoppedPorterService::class, static::class."@stopForService");
        $events->listen(BuiltDockerCompose::class, static::class."@adaptDockerCompose");
    }
}
