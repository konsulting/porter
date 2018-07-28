<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
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
        $workingDir = "";
        $version = $this->argument('version') ?: settings('default_cli_version');

        $project = app(Porter::class)->resolveProject();

        if ($project && ! empty($project['php'])) {
            $version = $project['php'];
            $workingDir = '-w /srv/app/'.$project['name'];
        }

        passthru(docker_compose("run {$workingDir} php_cli_{$version} bash"));
    }
}
