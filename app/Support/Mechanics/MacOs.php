<?php

namespace App\Support\Mechanics;

use App\Support\Mechanics\Exceptions\UnableToRetrieveIP;

class MacOs extends Untrained
{
    /** @var string Alternative Loopback Address */
    protected $alternativeLoopback = '10.200.10.1';

    /**
     * Trust the given root certificate file in the Keychain.
     *
     * @param string $pem
     *
     * @return void
     */
    public function trustCA($pem)
    {
        $this->consoleWriter->info('Auto Trust CA Certificate, needs sudo privilege. Please provide your sudo password.');

        $command = "sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain {$pem}";

        $this->cli->passthru($command);
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
        $this->consoleWriter->info('Auto Trust Certificate, needs sudo privilege. Please provide your sudo password.');

        $command = "sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain {$crt}";

        $this->cli->passthru($command);
    }

    /**
     * Return the User's home directory path.
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return $this->serverBag->get('HOME');
    }

    /**
     * Flush the host system DNS cache.
     *
     * @return void
     */
    public function flushDns()
    {
        $this->consoleWriter->info('Flushing DNS. Requires sudo permissions.');
        $this->cli->passthru('sudo killall -HUP mDNSResponder');
    }

    /**
     * Add the alternative loopback address to the system.
     *
     * Add a loopback alias to 10.200.10.1. This is then used as the IP for DNS resolution, otherwise
     * we get weird results when trying to access services hosted in docker (since they resolve
     * 127.0.0.1 to the requesting container).
     *
     * @return void
     */
    public function addAlternativeLoopbackAddress()
    {
        $this->consoleWriter->info("Adding loopback alias to {$this->alternativeLoopback}/24. Please provide your sudo password.");

        $command = "sudo ifconfig lo0 alias {$this->alternativeLoopback}/24";

        $this->cli->passthru($command);
    }

    /**
     * Remove the alternative loopback address from the system.
     *
     * @return void
     */
    public function removeAlternativeLoopbackAddress()
    {
        $this->consoleWriter->info("Removing loopback alias to {$this->alternativeLoopback}. Please provide your sudo password.");

        $command = "sudo ifconfig lo0 -alias {$this->alternativeLoopback}";

        $this->cli->passthru($command);
    }

    /**
     * Determine the working IP for Porter.
     *
     * @throws UnableToRetrieveIP
     *
     * @return string
     */
    public function getPorterDomainIp()
    {
        if (($records = dns_get_record('www.unlikely-domain-name.'.setting('domain'))) === []) {
            throw new UnableToRetrieveIP();
        }

        return $records[0]['ip'];
    }
}
