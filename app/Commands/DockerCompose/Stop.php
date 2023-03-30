<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Stop extends BaseCommand
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
     */
    public function handle(): void
    {
        $this->porter->stop();
    }
}
