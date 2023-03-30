<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;
use App\Models\Site;

class Open extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:open {run?} {--p|php-version=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open PHP cli';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($version = (string) $this->option('php-version')) {
            $version = PhpVersion::findByDirtyVersionNumber($version);
        } else {
            $version = optional(Site::resolveFromPathOrCurrentWorkingDirectory())->php_version
                ?: PhpVersion::defaultVersion();
        }

        $this->info("PHP Version: {$version->version_number}");

        $this->dockerCompose
            ->runContainer("php_cli_{$version->safe}")
            ->bash((string) $this->argument('run'))
            ->doNotTimeout()
            ->perform();
    }
}
