<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Logs extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logs {service?}';

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
        $this->porter->logs((string) $this->argument('service'));
    }
}
