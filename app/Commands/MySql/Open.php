<?php

namespace App\Commands\MySql;

use App\DockerCompose\CliCommand as DockerCompose;
use LaravelZero\Framework\Commands\Command;

class Open extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mysql:open';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open MySQL cli';

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

        DockerCompose::runContainer("mysql")
            ->append("mysql -h mysql -uroot -psecret")
            ->interactive()
            ->perform();
    }
}
