<?php

namespace App\Commands\Meilisearch;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Meilisearch off';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->turnOffService('meilisearch');
    }
}
