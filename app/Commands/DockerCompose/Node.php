<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
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
        $workingDir = "";
        $project = app(Porter::class)->resolveProject();

        if ($project && ! empty($project['name'])) {
            $workingDir = '-w /srv/app/'.$project['name'];
        }

        passthru(docker_compose("run {$workingDir} node bash"));
    }
}
