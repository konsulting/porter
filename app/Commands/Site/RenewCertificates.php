<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;
use App\Support\Ssl\CertificateBuilder;

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
        /** @var CertificateBuilder $builder */
        $builder = app(CertificateBuilder::class);
        $builder->clearCertificates((bool) $this->option('clear-ca'));

        // The porter_default certificate is used for serving error pages
        // when a domain has not been set up in Porter
        $builder->build('porter_default');

        foreach (Site::where('secure', true)->get() as $site) {
            $site->buildCertificate();
        }
    }
}
