<?php

namespace App\DockerCompose;

use App\Support\Cli;

class CliCommand
{
    protected $command = '';
    protected $interactive = false;
    protected $realTime = false;

    public function __construct($command)
    {
        $this->command = trim($command);
    }

    /**
     * Construct a docker-compose run {$container} command
     *
     * @param string|null $container
     *
     * @return CliCommand
     */
    public static function runContainer($container = null)
    {
        $site = site_from_cwd();

        $workingDir = $site ? '-w /srv/app/'.$site : '';

        return new static("run {$workingDir} --rm {$container}");
    }

    /**
     * Construct a docker-compose exec {$container} command
     *
     * @param string|null $container
     *
     * @return CliCommand
     */
    public static function execContainer($container = null)
    {
        return new static("exec {$container}");
    }

    /**
     * Construct a docker-compose command
     *
     * @param string|null $command
     *
     * @return CliCommand
     */
    public static function command($command = null)
    {
        return new static($command);
    }

    /**
     * Append a bash command, optionally with a further call
     *
     * @param string|null $command
     * @return $this
     */
    public function bash($command = null)
    {
        $this->interactive();
        $this->append("bash");

        if ($command) {
            $this->append(" -c \"$command\"");
        }

        return $this;
    }

    /**
     * Append to a command
     *
     * @param string|null $string
     * @return $this
     */
    public function append($string = null)
    {
        $this->command = trim($this->command . " {$string}");

        return $this;
    }

    /**
     * Set a command as being interactive (i.e. passthru() in php)
     *
     * @return $this
     */
    public function interactive()
    {
        $this->interactive = true;

        return $this;
    }

    /**
     * Set a command as not being interactive
     *
     * @return $this
     */
    public function notInteractive()
    {
        $this->interactive = false;

        return $this;
    }

    /**
     * Check if the command is expected to be interactive
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->interactive;
    }

    /**
     * Set our expectation to see real-time output
     *
     * @return $this
     */
    public function realTime()
    {
        $this->realTime = true;

        return $this;
    }

    /**
     * Set our expectation NOT to see real-time output
     *
     * @return $this
     */
    public function notRealTime()
    {
        $this->realTime = false;

        return $this;
    }

    /**
     * Check if we're expecting real0time output
     *
     * @return bool
     */
    public function isRealTime()
    {
        return $this->realTime;
    }

    /**
     * Prepare the full command string
     *
     * @return string
     */
    public function prepare()
    {
        return trim(
            'docker-compose -f '
                . config('app.docker-compose-file')
                . ' '
                . $this->command
        );
    }

    /**
     * Execute the command
     *
     * @return string|null
     */
    public function perform()
    {
        if ($this->isInteractive()) {
            return $this->getCli()->passthru($this->prepare());
        }

        if ($this->isRealTime()) {
            return $this->getCli()->execRealTime($this->prepare());
        }

        return $this->getCli()->exec($this->prepare());
    }

    /**
     * Get the Cli class
     *
     * @return Cli
     */
    protected function getCli()
    {
        return app(Cli::class);
    }
}
