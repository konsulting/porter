<?php

namespace App\Commands\Php;

use App\DockerCompose\CliCommand as DockerCompose;
use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class Open extends Command
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

        DockerCompose::runContainer("php_cli_{$version->safe}")
            ->bash($this->argument('run'))
            ->perform();
    }
}
