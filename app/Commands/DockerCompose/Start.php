<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Start extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create and start the containers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->start();
    }
}
