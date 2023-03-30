<?php

namespace App\Commands\MySql;

use App\Commands\BaseCommand;

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
     */
    public function handle(): void
    {
        if (setting('use_mysql', 'on') !== 'on') {
            $this->error('Not using docker mysql');

            return;
        }

        $this->dockerCompose
            ->runContainer('mysql')
            ->append('mysql -h mysql -uroot -psecret')
            ->interactive()
            ->doNotTimeout()
            ->perform();
    }
}
