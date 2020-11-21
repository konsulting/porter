<?php

namespace App\Support\Console\DockerCompose;

use App\PorterLibrary;
use App\Support\Contracts\Cli;

class CliCommand
{
    protected $command = '';
    protected $interactive = false;
    protected $realTime = false;

    /**
     * @var Cli
     */
    protected $cli;

    public function __construct(Cli $cli, $command)
    {
        $this->command = trim($command);
        $this->cli = $cli;
    }

    /**
     * Append a bash command, optionally with a further call.
     *
     * @param string|null $command
     *
     * @return $this
     */
    public function bash($command = null)
    {
        $this->interactive();
        $this->append('bash');

        if ($command) {
            $this->append("-c \"$command\"");
        }

        return $this;
    }

    /**
     * Append to a command.
     *
     * @param string|null $string
     *
     * @return $this
     */
    public function append($string = null)
    {
        $this->command = trim($this->command." {$string}");

        return $this;
    }

    /**
     * Set a command as being interactive (i.e. passthru() in php).
     *
     * @return $this
     */
    public function interactive()
    {
        $this->interactive = true;

        return $this;
    }

    /**
     * Set a command as not being interactive.
     *
     * @return $this
     */
    public function notInteractive()
    {
        $this->interactive = false;

        return $this;
    }

    /**
     * Check if the command is expected to be interactive.
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->interactive;
    }

    /**
     * Set our expectation to see real-time output.
     *
     * @return $this
     */
    public function realTime()
    {
        $this->realTime = true;

        return $this;
    }

    /**
     * Set our expectation NOT to see real-time output.
     *
     * @return $this
     */
    public function notRealTime()
    {
        $this->realTime = false;

        return $this;
    }

    /**
     * Check if we're expecting real0time output.
     *
     * @return bool
     */
    public function isRealTime()
    {
        return $this->realTime;
    }

    /**
     * Prepare the full command string.
     *
     * @return string
     */
    public function prepare()
    {
        return trim(
            'docker-compose -f '
            .app(PorterLibrary::class)->dockerComposeFile()
            .' -p porter '
            .$this->command
        );
    }

    /**
     * Execute the command.
     *
     * @return string|int
     */
    public function perform()
    {
        if ($this->isInteractive()) {
            return $this->cli->passthru($this->prepare());
        }

        if ($this->isRealTime()) {
            return $this->cli->execRealTime($this->prepare());
        }

        return $this->cli->exec($this->prepare());
    }

    /**
     * Return the Cli instance for this CliCommand.
     *
     * @return Cli
     */
    public function getCli()
    {
        return $this->cli;
    }

    /**
     * Set the timeout for the Cli instance.
     *
     * @param int $seconds
     *
     * @return CliCommand
     */
    public function setTimeout(int $seconds)
    {
        $this->cli->setTimeout($seconds);

        return $this;
    }

    /**
     * Remove the timeout for the Cli instance. (Just a nicer way to write it).
     *
     * @return CliCommand
     */
    public function doNotTimeout()
    {
        $this->cli->doNotTimeout();

        return $this;
    }
}
