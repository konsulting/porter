<?php

namespace App\Commands\Node;

use App\Commands\BaseCommand;

class Open extends BaseCommand
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
        $this->dockerCompose
            ->runContainer('node')
            ->bash((string) $this->argument('run'))
            ->perform();
    }
}
