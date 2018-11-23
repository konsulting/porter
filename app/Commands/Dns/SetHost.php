<?php

namespace App\Commands\Dns;

use App\Commands\BaseCommand;
use App\Support\Dnsmasq\Config;
use App\Support\Mechanics\Mechanic;

class SetHost extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dns:set-host {--restore}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the host based on the OS';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $mechanic = app(Mechanic::class);

        if ($this->option('restore')) {
            $mechanic->restoreNetworking();
            app(Config::class)->updateIp('127.0.0.1');

            return;
        }

        $mechanic->setupNetworking();
        app(Config::class)->updateIp($mechanic->getHostAddress());
    }
}
