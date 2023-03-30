<?php

namespace App\Commands\Elasticsearch;

use App\Commands\BaseCommand;

class Off extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Elasticsearch off';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->porter->turnOffService('elasticsearch');
    }
}
