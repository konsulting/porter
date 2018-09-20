<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Restart extends BaseCommand
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
        $this->porter->restart((string) $this->argument('service'));
    }
}
