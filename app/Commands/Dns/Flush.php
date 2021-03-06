<?php

namespace App\Commands\Dns;

use App\Commands\BaseCommand;

class Flush extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dns:flush';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Flush your host DNS';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porterLibrary->getMechanic()->flushDns();
    }
}
