<?php

namespace App\Commands\MySql;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class On extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mysql:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn MySQL on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->turnOnService('mysql');
    }
}
