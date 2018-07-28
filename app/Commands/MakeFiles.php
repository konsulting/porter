<?php

namespace App\Commands;

use App\DockerCompose\YamlBuilder;
use App\Nginx\SiteConfBuilder;
use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MakeFiles extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make-files';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '(Re)make the files we need';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $wasUp = app(Porter::class)->isUp();

        if ($wasUp) {
            $this->call('stop');
        }

        app(YamlBuilder::class)->build();

        // Build Nginx Files
        foreach(app(Porter::class)->getSettings()->get('projects') as $project) {
            app(SiteConfBuilder::class)->build($project);
        }

        if ($wasUp) {
            $this->call('start');
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
