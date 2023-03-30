<?php

namespace App\Commands\DockerSync;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker-sync:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn docker sync off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('docker-sync');
    }
}
