<?php

namespace App\Commands\Site;

use App\Site;
use LaravelZero\Framework\Commands\Command;

class Secure extends Command
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
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);
        $site->secure();
    }
}
