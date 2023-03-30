<?php

namespace App\Commands\Dns;

use App\Commands\BaseCommand;
use App\Support\Dnsmasq\Config;

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
     */
    public function handle(): void
    {
        if ($this->option('restore')) {
            $this->porterLibrary->getMechanic()->removeAlternativeLoopbackAddress();
            app(Config::class)->updateIp($this->porterLibrary->getMechanic()->getStandardLoopback());
            $this->porter->restart('dns');

            return;
        }

        $this->porterLibrary->getMechanic()->addAlternativeLoopbackAddress();
        app(Config::class)->updateIp($this->porterLibrary->getMechanic()->getAlternativeLoopback());
        $this->porter->restart('dns');
    }
}
