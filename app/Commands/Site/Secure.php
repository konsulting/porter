<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;

class Secure extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:secure {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set up a site to use https';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: Site::nameFromPath($this->cli->currentWorkingDirectory());

        if (!$name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);
        $site->secure();
    }
}
