<?php

namespace App\Commands\Redis;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'redis:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Redis off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('redis');
    }
}
