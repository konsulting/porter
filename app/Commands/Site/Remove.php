<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;

class Remove extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:remove {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a site';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        Site::resolveFromPathOrCurrentWorkingDirectoryOrFail($this->argument('site'))->remove();
    }
}
