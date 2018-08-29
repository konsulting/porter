<?php

namespace App\Commands\MySql;

use App\Commands\BaseCommand;
use App\DockerCompose\CliCommandFactory;

class Open extends BaseCommand
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

        app(CliCommandFactory::class)
            ->runContainer("mysql")
            ->append("mysql -h mysql -uroot -psecret")
            ->interactive()
            ->perform();
    }
}
