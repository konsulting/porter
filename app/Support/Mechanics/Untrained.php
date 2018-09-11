<?php

namespace App\Support\Mechanics;


use App\Support\Console\ConsoleWriter;
use App\Support\Mechanics\Mechanic;

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
        $this->iAmNotTrainedTo('trust a CA certificate');
    }

    /**
     * Trust the given certificate file in the Mac Keychain.
     *
     * @param  string  $crt
     * @return void
     */
    public function trustCertificate($crt)
    {
        $this->iAmNotTrainedTo('trust a certificate');
    }

    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath()
    {
        $this->iAmNotTrainedTo('get the users home path');
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

    /**
     * Give a nice message about not being trained
     *
     * @param $activity
     */
    protected function iAmNotTrainedTo($activity)
    {
        $this->console->info("I haven't been trained to {$activity} on this system.");
        $this->console->info("You are welcome to train me and submit a PR.");
    }
}
