<?php

namespace App\Commands\DockerCompose;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Stop extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stop';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Stop and remove the containers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        exec(docker_compose("down"));
    }
}
