<?php

namespace App\Support\Mechanics;

class MacOs extends Untrained
{
    /**
     * Trust the given root certificate file in the Keychain.
     *
     * @param  string  $pem
     * @return void
     */
    public function trustCA($pem)
    {
        $this->consoleWriter->info('Auto Trust CA Certificate, needs sudo privilege. Please provide your sudo password.');

        $command = "sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain {$pem}";

        if($this->isTesting()) {
            $this->consoleWriter->info("Did not trust CA during testing.");
            return;
        }

        $this->cli->passthru($command);
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param  string  $crt
     * @return void
     */
    public function trustCertificate($crt)
    {
        $this->consoleWriter->info('Auto Trust Certificate, needs sudo privilege. Please provide your sudo password.');

        $command = "sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain {$crt}";

        if($this->isTesting()) {
            $this->consoleWriter->info("Did not trust Certificate during testing.");
            return;
        }

        $this->cli->passthru($command);
    }

    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return $this->serverBag->get('HOME');
    }

    /**
     * Flush the host system DNS cache
     *
     * @return void
     */
    public function flushDns()
    {
        $this->consoleWriter->info('Flushing DNS. Requires sudo permissions.');
        $this->cli->passthru('sudo killall -HUP mDNSResponder');
    }
}
