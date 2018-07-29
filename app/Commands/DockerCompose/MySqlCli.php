<?php

namespace App\Commands\DockerCompose;

use LaravelZero\Framework\Commands\Command;

class MySqlCli extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mysql';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open MySql Cli';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_mysql') != 'on') {
            $this->error('Not using docker mysql');
            return;
        }

        passthru(docker_compose("run mysql mysql -h mysql -uroot -psecret"));
    }
}
