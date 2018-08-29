<?php

namespace App\Commands\DockerCompose;

use App\Commands\BaseCommand;

class Build extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '(Re)build the containers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->build();
    }
}
