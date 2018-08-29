<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\DockerCompose\CliCommandFactory;
use App\PhpVersion;
use App\Site;

class Tinker extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:tinker';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open tinker in a Laravel project';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = site_from_cwd();
        $site = Site::where('name', $name)->first();

        if (! $site) {
            $this->error('Please run this command in a project directory');
            return;
        }

        $version = optional($site)->php_version ?: PhpVersion::defaultVersion();

        $this->info("PHP Version: {$version->version_number}");

        app(CliCommandFactory::class)
            ->runContainer("php_cli_{$version->safe}")
            ->bash("php artisan tinker")
            ->perform();
    }
}
