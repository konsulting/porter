<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\PhpVersion;
use App\Site;

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
     *
     * @return void
     */
    public function handle(): void
    {
        if ($version = $this->option('php-version')) {
            $version = PhpVersion::findByDirtyVersionNumber($version);
        } else {
            $name = site_from_cwd();
            $site = Site::where('name', $name)->first();
            $version = optional($site)->php_version ?: PhpVersion::defaultVersion();
        }

        $this->info("PHP Version: {$version->version_number}");

        $this->dockerCompose
            ->runContainer("php_cli_{$version->safe}")
            ->bash($this->argument('run'))
            ->perform();
    }
}
