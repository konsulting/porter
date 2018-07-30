<?php

namespace App\Commands\Node;

use LaravelZero\Framework\Commands\Command;

class Open extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'node:open';

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
