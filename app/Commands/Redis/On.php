<?php

namespace App\Commands\Redis;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class On extends Command
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
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->turnOnService('redis');
    }
}
