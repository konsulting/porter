<?php

namespace App\Support\Mechanics;

use App\Support\Console\Cli;
use App\Support\Console\ServerBag;
use Illuminate\Support\MessageBag;

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
        $this->console->info('Auto Trust CA Certificate, needs sudo privilege. Please provide your sudo password.');

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
        $this->console->info('Auto Trust Certificate, needs sudo privilege. Please provide your sudo password.');

        $command = "sudo security add-trusted-cert -d -r trustAsRoot -k /Library/Keychains/System.keychain {$crt}";
        $this->commands[] = $command;

        if($this->isTesting()) {
            $this->console->info("Did not trust Certificate during testing.");
            return;
        }

        app(Cli::class)->passthru($command);
    }

    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        return app(ServerBag::class)->get('HOME');
    }
}
