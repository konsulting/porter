<?php

namespace App\Ssl\Trust\Mechanics;


use App\Ssl\Trust\Mechanic;
use App\Support\Console\ConsoleWriter;

class Untrained implements Mechanic
{
    /** @var array */
    protected $commands = [];

    /** @var ConsoleWriter */
    protected $console;

    /**
     * Untrained constructor.
     *
     * @param \App\Support\Console\ConsoleWriter $console
     */
    public function __construct(ConsoleWriter $console)
    {
        $this->console = $console;
    }

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
