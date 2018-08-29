<?php

namespace App\Commands;

use App\DockerCompose\CliCommandFactory;
use App\Porter;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    /** @var Porter */
    protected $porter;

    /** @var CliCommandFactory  */
    protected $dockerCompose;

    public function __construct(Porter $porter, CliCommandFactory $dockerCompose)
    {
        parent::__construct();

        $this->porter = $porter;
        $this->dockerCompose = $dockerCompose;
    }
}
