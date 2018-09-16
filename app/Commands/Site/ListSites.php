<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;
use App\Models\Site;

class ListSites extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:list';

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
                    $site->nginx_conf,
                    $site->scheme.$site->url,
                ];
            });

        $this->table($headers, $sites);
        $this->info('The default PHP version is '.PHPVersion::defaultVersion()->version_number);
    }
}
