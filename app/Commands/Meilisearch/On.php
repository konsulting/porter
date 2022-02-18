<?php

namespace App\Commands\Meilisearch;

use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Meilisearch on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->turnOnService('meilisearch');
    }
}
