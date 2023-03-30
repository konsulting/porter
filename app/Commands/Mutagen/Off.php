<?php

namespace App\Commands\Mutagen;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mutagen:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn mutagen off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('mutagen');
    }
}
