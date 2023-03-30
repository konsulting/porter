<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class SoftRestart extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'soft-restart {service?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Soft restart containers (ignores after config changes)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->softRestart((string) $this->argument('service'));
    }
}
