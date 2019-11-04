<?php

namespace App\Commands\Mutagen;

use App\Commands\BaseCommand;
use App\Support\Mutagen\Mutagen;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mutagen:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn mutagen on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->turnOnService('mutagen');
    }
}
