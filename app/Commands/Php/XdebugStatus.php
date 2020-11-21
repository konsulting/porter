<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\Support\XDebug\XDebug;

class XdebugStatus extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:xdebug {status}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set XDebug status On/Off';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        $status = strtolower(/** @scrutinizer ignore-type */ $this->argument('status'));

        if (!in_array($status, ['on', 'off'])) {
            throw new \Exception('Xdebug can only be turned on or off.');
        }

        if ($status === 'on') {
            app(XDebug::class)->turnOn();

            return;
        }

        app(XDebug::class)->turnOff();
    }
}
