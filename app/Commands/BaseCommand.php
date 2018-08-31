<?php

namespace App\Commands;

use App\DockerCompose\CliCommandFactory;
use App\Porter;
use App\Support\Cli;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    /** @var Cli  */
    protected $cli;

    /** @var CliCommandFactory  */
    protected $dockerCompose;

    /** @var Porter */
    protected $porter;

    public function __construct(Cli $cli, CliCommandFactory $dockerCompose, Porter $porter)
    {
        parent::__construct();

        $this->cli = $cli;
        $this->dockerCompose = $dockerCompose;
        $this->porter = $porter;
    }
}
