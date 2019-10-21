<?php

namespace App\Support\Contracts;

interface Cli
{
    /**
     * Execute a command.
     *
     * @param string $command
     *
     * @return string
     */
    public function exec($command);

    /**
     * Execute a command in real time.
     *
     * @param string $command
     *
     * @return string
     */
    public function execRealTime($command);

    /**
     * Execute a command and allow the user to interact with it.
     *
     * @param string $command
     *
     * @return void
     */
    public function passthru($command);

    /**
     * Return the current working directory.
     *
     * @return string
     */
    public function currentWorkingDirectory();

    /**
    * Set the timeout for the wrapping PHP Process.
    *
    * @param int|null $seconds
    *
    * @return Cli
    */
    public function setTimeout(int $seconds = null);

    /**
     * Return the timeout for the wrapping PHP Process.
     *
     * @return int|null
     */
    public function getTimeout();
}
