<?php

namespace App\Commands\Sites;

use App\Site;
use App\Ssl\CertificateBuilder;
use LaravelZero\Framework\Commands\Command;

class RenewCertificates extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:renew-certs {--clear-ca}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Renew certificates';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(CertificateBuilder::class)->clearDirectory((bool) $this->option('clear-ca'));

        foreach (Site::where('secure', true)->get() as $site) {
            $site->buildCertificate();
        }
    }
}
