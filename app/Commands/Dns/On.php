<?php

namespace App\Commands\Dns;

use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dns:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn DNS container on';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOnService('dns');
    }
}
