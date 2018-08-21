<?php

namespace App\Commands\Node;

use App\DockerCompose\CliCommand as DockerCompose;
use LaravelZero\Framework\Commands\Command;

class Open extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'node:open {run?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open Node cli';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        DockerCompose::runContainer("node")
            ->bash($this->argument('run'))
            ->perform();
    }
}
