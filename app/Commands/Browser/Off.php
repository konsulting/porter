<?php

namespace App\Commands\Browser;

use App\Commands\BaseCommand;

class Off extends BaseCommand
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
     */
    public function handle(): void
    {
        $this->porter->turnOffService('browser');
    }
}
