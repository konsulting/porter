<?php

namespace App\Support\Ssl\Trust\Mechanics;

use App\Support\Console\Cli;
use App\Support\Ssl\Trust\Mechanic;

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
        $this->console->info('Auto Trust CA Certificate, needs sudo privilege.');

        $command = "sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain {$pem}";
        $this->commands[] = $command;

        if($this->isTesting()) {
            $this->console->info("Did not trust CA during testing.");
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
        $this->console->info('Auto Trust Certificate, needs sudo privilege.');

        $command = "sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain {$crt}";
        $this->commands[] = $command;

        if($this->isTesting()) {
            $this->console->info("Did not trust Certificate during testing.");
            return;
        }

        app(Cli::class)->passthru($command);
    }
}
