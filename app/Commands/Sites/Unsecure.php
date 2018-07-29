<?php

namespace App\Commands\Sites;

use App\Site;
use LaravelZero\Framework\Commands\Command;

class Unsecure extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:unsecure {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Unsecure a site';

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
        $site->unsecure();
    }
}
