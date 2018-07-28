<?php

namespace App\Commands\Sites;

use App\Porter;
use App\Ssl\CertificateBuilder;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Unsecure extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'unsecure {site?}';

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
        $site = $this->argument('site');

        if (! $site && $project = app(Porter::class)->resolveProject()) {
            $site = $project['name'];
        }

        // remove certs

        app(Porter::class)->updateProject($site, ['secure' => false]);
    }
}
