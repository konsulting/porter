<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Site;

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
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $site = Site::where('name', $name)->first()) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site->remove();
    }
}
