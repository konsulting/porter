<?php

namespace App\Support\Mechanics;

use App\Support\Console\ConsoleWriter;
use App\Support\Console\ServerBag;
use App\Support\Contracts\Cli;

class Untrained implements Mechanic
{
    /** @var Cli */
    protected $cli;

    /** @var ConsoleWriter */
    protected $consoleWriter;

    /** @var ServerBag */
    protected $serverBag;

    /**
     * Untrained constructor.
     *
     * @param Cli           $cli
     * @param ConsoleWriter $consoleWriter
     * @param ServerBag     $serverBag
     */
    public function __construct(Cli $cli, ConsoleWriter $consoleWriter, ServerBag $serverBag)
    {
        $this->cli = $cli;
        $this->consoleWriter = $consoleWriter;
        $this->serverBag = $serverBag;
    }

    /**
     * Trust the given root certificate file in the Keychain.
     *
     * @param string $pem
     *
     * @return void
     */
    public function trustCA($pem)
    {
        $this->iAmNotTrainedTo('trust a CA certificate');
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param string $crt
     *
     * @return void
     */
    public function trustCertificate($crt)
    {
        $this->iAmNotTrainedTo('trust a certificate');
    }

    /**
     * Return the User's home directory path.
     *
     * @return string
     */
    public function getUserHomePath()
    {
        $this->iAmNotTrainedTo('get the users home path');
    }

    /**
     * Flush the host system DNS cache.
     *
     * @return void
     */
    public function flushDns()
    {
        $this->iAmNotTrainedTo('flush the DNS');
    }

    /**
     * Give a nice message about not being trained.
     *
     * @param $activity
     */
    protected function iAmNotTrainedTo($activity)
    {
        $this->consoleWriter->info("I haven't been trained to {$activity} on this system.");
        $this->consoleWriter->info('You are welcome to train me and submit a PR.');
    }
}
