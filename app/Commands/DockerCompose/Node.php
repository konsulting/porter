<?php

namespace App\Commands\DockerCompose;

use LaravelZero\Framework\Commands\Command;

class Node extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'node';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open Node cli';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = site_from_cwd();
        $workingDir = $name ? '-w /srv/app/'.$name : '';

        passthru(docker_compose("run {$workingDir} node bash"));
    }
}
