<?php

namespace App\Commands;

use App\Models\Site;
use App\Support\Ssl\CertificateBuilder;

class MakeFiles extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make-files';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '(Re)make the files we need';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $wasUp = $this->porter->isUp();

        if ($wasUp) {
            $this->call('stop');
        }

        $this->porter->compose();

        foreach (Site::all() as $site) {
            $site->buildFiles();
        }

        // The porter_default certificate is used for serving error pages
        // when a domain has not been set up in Porter
        app(CertificateBuilder::class)->build('porter_default');

        if ($wasUp) {
            $this->call('start');
        }
    }
}
