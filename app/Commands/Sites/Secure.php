<?php

namespace App\Commands\Sites;

use App\Porter;
use App\Ssl\CertificateBuilder;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Secure extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'secure {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Secure a site';

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

        (new CertificateBuilder(storage_path('ssl')))
            ->build($site.'.'.settings('tld'));

        app(Porter::class)->updateProject($site, ['secure' => true]);
    }
}
