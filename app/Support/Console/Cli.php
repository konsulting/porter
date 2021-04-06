<?php

namespace App\Support\Console;

use App\Support\Contracts\Cli as CliContract;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Cli implements CliContract
{
    /**
     * The process timeout in seconds.
     *
     * @var int|null
     */
    protected $timeout = null;

    /**
     * Execute a command.
     *
     * @param string $command
     *
     * @return string
     */
    public function exec($command)
    {
        $process = $this->getProcess($command);
        $process->run();

        return $process->getOutput();
    }

    /**
     * Execute a command in real time.
     *
     * @param string $command
     *
     * @return int
     */
    public function execRealTime($command)
    {
        $process = $this->getProcess($command);

        try {
            $process->mustRun(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }

        return $process->getExitCode();
    }

    /**
     * Execute a command and allow the user to interact with it.
     *
     * @param string $command
     *
     * @return int
     */
    public function passthru($command)
    {
        $process = $this->getProcess($command);

        try {
            $process->setTty(true);
            $process->mustRun(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();
        }

        return $process->getExitCode();
    }

    /**
     * Get a Symfony process object that can execute a command.
     *
     * @param string $command The command to execute
     *
     * @return Process
     */
    protected function getProcess($command)
    {
        return app()->make(Process::class, [
            'command'     => [$command],
            'timeout'     => $this->timeout,
        ]);
    }

    /**
     * Return the current working directory.
     *
     * @return string
     */
    public function currentWorkingDirectory()
    {
        return getcwd();
    }

    /**
     * Set the timeout for the wrapping PHP Process.
     *
     * @param int $seconds
     *
     * @return Cli
     */
    public function setTimeout(int $seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Remove the timeout for the wrapping PHP Process.
     *
     * @return Cli
     */
    public function doNotTimeout()
    {
        $this->timeout = null;

        return $this;
    }

    /**
     * Return the timeout for the wrapping PHP Process.
     *
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
