<?php

namespace App\Commands\Redis;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class Off extends Command
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
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->turnOffService('redis');
    }
}
