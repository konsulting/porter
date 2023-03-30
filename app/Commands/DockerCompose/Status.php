<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Status extends BaseCommand
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
     */
    public function handle(): void
    {
        $this->porter->status();
    }
}
