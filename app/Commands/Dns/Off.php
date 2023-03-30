<?php

namespace App\Commands\Dns;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dns:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn DNS container off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('dns');
    }
}
