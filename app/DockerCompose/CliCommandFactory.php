<?php

namespace App\DockerCompose;

use App\Support\Contracts\Cli;

class CliCommandFactory
{
    /**
     * @var Cli
     */
    protected $cli;

    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    /**
     * Create a Docker CLI command.
     *
     * @param string $command
     * @return CliCommand
     */
    public function command($command)
    {
        return new CliCommand($this->cli, $command);
    }

//    /**
//     * Construct a docker-compose exec {$container} command
//     *
//     * @param string|null $container
//     * @return CliCommand
//     */
//    public function execContainer($container = null)
//    {
//        return new CliCommand($this->cli, "exec {$container}");
//    }
//
//    /**
//     * Construct a docker-compose run {$container} command
//     *
//     * @param string|null $container
//     * @return CliCommand
//     */
//    public function runContainer($container = null)
//    {
//        $site = site_from_cwd();
//        $workingDir = $site ? '-w /srv/app/' . $site : '';
//
//        return new CliCommand($this->cli, "run {$workingDir} --rm {$container}");
//    }
}
