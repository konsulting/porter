<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;
use App\Ssl\CertificateBuilder;

class RenewCertificates extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:renew-certs {--clear-ca}';

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
