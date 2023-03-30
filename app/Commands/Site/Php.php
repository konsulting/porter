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
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectoryOrFail((string) $this->argument('site'));

        $option = $this->menu(
            'Available PHP versions',
            PhpVersion::getList($site->php_version->version_number)
        )->open();

        if (!$option) {
            return;
        }

        $site->setPhpVersion($option);
    }
}
