<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Logs extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logs';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show container logs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->logs();
    }
}
