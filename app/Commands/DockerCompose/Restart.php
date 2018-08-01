<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Restart extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'restart {service?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart containers (e.g. after config changes)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->restart($this->argument('service'));
    }
}
