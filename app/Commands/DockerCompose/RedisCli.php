<?php

namespace App\Commands\DockerCompose;

use LaravelZero\Framework\Commands\Command;

class RedisCli extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'redis';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open Redis-Cli';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_redis') != 'on') {
            $this->error('Not using docker redis');
            return;
        }

        passthru(docker_compose("run redis redis-cli -h redis"));
    }
}
