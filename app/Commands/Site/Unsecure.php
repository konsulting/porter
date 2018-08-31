<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Site;

class Unsecure extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:unsecure {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set up a site to use http';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: Site::nameFromPath($this->cli->currentWorkingDirectory());

        if (! $name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);
        $site->unsecure();
    }
}
