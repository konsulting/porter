<?php

namespace App\Commands\Browser;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class On extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browser:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Browser on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->turnOnService('browser');
    }
}
