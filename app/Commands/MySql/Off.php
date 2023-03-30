<?php

namespace App\Commands\MySql;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mysql:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn MySQL off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('mysql');
    }
}
