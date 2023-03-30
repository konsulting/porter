<?php

namespace App\Commands\MySql;

use App\Commands\BaseCommand;

class On extends BaseCommand
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
     */
    public function handle(): void
    {
        $this->porter->turnOnService('mysql');
    }
}
