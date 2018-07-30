<?php

namespace App\Commands\Sites;

use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class ListSites extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List available sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $headers = ['Name', 'PHP Version', 'NGiNX Template', 'Url'];

        $sites = Site::with('php_version')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($site) {
                return [
                    $site->name,
                    $site->php_version->version_number,
                    $site->nginx_type,
                    'http'.($site->secure ? 's' : '').'://'.$site->url,
                ];
            });

        $this->table($headers, $sites);
        $this->info('The default PHP version is '. PHPVersion::defaultVersion()->version_number);
    }
}
