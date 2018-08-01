<?php

namespace App\Ssl\Trust\Mechanics;


use App\Ssl\Trust\Mechanic;

class Untrained implements Mechanic
{
    protected $commands = [];

    /**
     * Trust the given root certificate file in the Keychain.
     *
     * @param  string  $pem
     * @return void
     */
    public function trustCA($pem)
    {
        //
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param  string  $crt
     * @return void
     */
    public function trustCertificate($crt)
    {
        //
    }

    /**
     * Check if we're running in test mode
     *
     * @return bool
     */
    public function isTesting()
    {
        return config('app.env') == 'testing';
    }

    /**
     * Return commands we've run
     *
     * @return mixed
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
