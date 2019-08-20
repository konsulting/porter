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

    /** @var string $hostAddress Address for Host */
    protected $hostAddress = '127.0.0.1';

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

    /**
     * Setup networking for Porter.
     *
     * @return void
     */
    public function setupNetworking()
    {
        $this->iAmNotTrainedTo('set up special networking for Porter');
    }

    /**
     * Restore networking for Porter.
     *
     * @return void
     */
    public function restoreNetworking()
    {
        $this->iAmNotTrainedTo('restore special networking for Porter');
    }

    /**
     * Get Host IP for Porter.
     *
     * @return string
     */
    public function getHostAddress()
    {
        return $this->hostAddress;
    }

    /**
     * Does a porter domain resolve to the Host Address
     *
     * @return bool
     */
    public function isUsingHostAddress()
    {
        $this->iAmNotTrainedTo('determine if we are using the Host Address for Porter');
    }

    /**
     * Does a porter domain resolve to 127.0.0.1
     *
     * @return bool
     */
    public function isUsingDefaultHostAddress()
    {
        $this->iAmNotTrainedTo('determine if we are using the Default Host Address for Porter');
    }
}
