<?php

namespace App\Commands\Redis;

use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'redis:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Redis on';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOnService('redis');
    }
}
