<?php

namespace App\Commands\Elasticsearch;

use App\Commands\BaseCommand;

class On extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Elasticsearch on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->porter->turnOnService('elasticsearch');
    }
}
