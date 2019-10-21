<?php

namespace App\Support\Console;

use App\Support\Contracts\Cli as CliContract;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Cli implements CliContract
{
    /**
     * The process timeout in seconds.
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
     * @return string
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
    }

    /**
     * Execute a command and allow the user to interact with it.
     *
     * @param string $command
     *
     * @return void
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
            'command'     => $command,
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
     * @param int|null $seconds
     *
     * @return Cli
     */
    public function setTimeout(int $seconds = null)
    {
        $this->timeout = $seconds;

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
