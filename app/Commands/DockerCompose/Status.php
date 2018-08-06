<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Status extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'status';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check the status of the containers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->status();
    }
}
