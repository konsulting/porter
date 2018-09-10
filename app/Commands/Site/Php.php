<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;
use App\Models\Site;

class Php extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:php {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Choose the PHP version for a site';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectoryOrFail($this->argument('site'));

        $currentVersion = $site->php_version->version_number;

        $option = $this->menu(
            'Available PHP versions',
            PhpVersion::pluck('version_number', 'id')
                ->map(function ($version) use ($currentVersion) {
                    return $version . ($version == $currentVersion ? ' (current)' : '');
                })->toArray()
        )->open();

        if (! $option) {
            return;
        }

        $site->setPhpVersion($option);
    }
}
