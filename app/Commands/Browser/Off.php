<?php

namespace App\Commands\Browser;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class Off extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browser:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Browser off';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->turnOffService('browser');
    }
}
