<?php

namespace App\Commands\DockerCompose;

use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class Php extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php {version?}';

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

        $site = Site::where('name', $name)->first();
        $version = optional($site->php_version->safe, PhpVersion::defaultVersion()->safe);

        passthru(docker_compose("run {$workingDir} php_cli_{$version} bash"));
    }
}
