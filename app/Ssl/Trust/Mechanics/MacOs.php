<?php

namespace App\Ssl\Trust\Mechanics;

use App\Ssl\Trust\Mechanic;
use App\Support\Cli;

class MacOs extends Untrained implements Mechanic
{
    /**
     * Trust the given root certificate file in the Keychain.
     *
     * @param  string  $pem
     * @return void
     */
    public function trustCA($pem)
    {
        console_writer('Auto Trust CA Certificate, needs sudo privilege.');

        $command = "sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain {$pem}";
        $this->commands[] = $command;

        if($this->isTesting()) {
            console_writer("Did not trust CA during testing.");
            return;
        }

        app(Cli::class)->passthru($command);
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param  string  $crt
     * @return void
     */
    public function trustCertificate($crt)
    {
        console_writer('Auto Trust Certificate, needs sudo privilege.');

        $command = "sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain {$crt}";
        $this->commands[] = $command;

        if($this->isTesting()) {
            console_writer("Did not trust Certificate during testing.");
            return;
        }

        app(Cli::class)->passthru($command);
    }
}
