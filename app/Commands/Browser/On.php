<?php

namespace App\Commands\Browser;

use App\Commands\BaseCommand;

class On extends BaseCommand
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
     */
    public function handle(): void
    {
        $this->porter->turnOnService('browser');
    }
}
