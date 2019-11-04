<?php

namespace App\Commands\DockerSync;

use App\Commands\BaseCommand;
use App\Support\DockerSync\DockerSync;

class Install extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker-sync:install';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install docker sync (mac only)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(DockerSync::class)->install();
    }
}
