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

    /** @var string Standard Loopback Address */
    protected $standardLoopback = '127.0.0.1';

    /** @var string Alternative Loopback Address */
    protected $alternativeLoopback = '127.0.0.1';

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
    public function addAlternativeLoopbackAddress()
    {
        $this->iAmNotTrainedTo('set up special networking for Porter');
    }

    /**
     * Restore networking for Porter.
     *
     * @return void
     */
    public function removeAlternativeLoopbackAddress()
    {
        $this->iAmNotTrainedTo('restore special networking for Porter');
    }

    /**
     * Get standard loopback address.
     *
     * @return string
     */
    public function getStandardLoopback()
    {
        return $this->standardLoopback;
    }

    /**
     * Get alternative loopback address.
     *
     * @return string
     */
    public function getAlternativeLoopback()
    {
        return $this->alternativeLoopback;
    }

    /**
     * Does a Porter domain resolve to the standard loopback address.
     *
     * @return bool
     */
    public function isUsingAlternativeLoopback()
    {
        return $this->getPorterDomainIp() === $this->getAlternativeLoopback();
    }

    /**
     * Does a Porter domain resolve to the standard loopback address?
     *
     * @return bool
     */
    public function isUsingStandardLoopback()
    {
        return $this->getPorterDomainIp() === $this->getStandardLoopback();
    }

    /**
     * Determine the working IP for Porter.
     *
     * @return string
     */
    public function getPorterDomainIp()
    {
        $this->iAmNotTrainedTo('obtain the current IP address for Porter');
    }
}
