<?php

namespace App\Commands\Redis;

use App\DockerCompose\CliCommandFactory;
use LaravelZero\Framework\Commands\Command;

class Open extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'redis:open';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open Redis cli';

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

        app(CliCommandFactory::class)
            ->runContainer("redis")
            ->append("redis-cli -h redis")
            ->interactive()
            ->perform();
    }
}
