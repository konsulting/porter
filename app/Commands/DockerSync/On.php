<?php

namespace App\Commands\DockerSync;

use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker-sync:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn docker sync on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->turnOnService('docker-sync');
    }
}
