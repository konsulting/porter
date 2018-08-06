<?php

namespace App\Commands\Php;

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
        $name = site_from_cwd();
        $workingDir = $name ? '-w /srv/app/'.$name : '';

        if ($version = $this->option('php-version')) {
            $version = PhpVersion::findByDirtyVersionNumber($version);
        } else {
            $site = Site::where('name', $name)->first();
            $version = optional($site)->php_version ?: PhpVersion::defaultVersion();
        }

        $this->info("PHP Version: {$version->version_number}");

        $run = $this->argument('run') ? sprintf('-c "%s"', $this->argument('run')): '';

        passthru(docker_compose("run {$workingDir} --rm php_cli_{$version->safe} bash {$run}"));
    }
}
