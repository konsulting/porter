<?php

namespace App\Commands;

use App\Porter;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\DockerCompose\CliCommandFactory;
use LaravelZero\Framework\Commands\Command;
use NunoMaduro\LaravelConsoleMenu\Menu;

/**
 * @method Menu menu(string $title, array $options)
 */
abstract class BaseCommand extends Command
{
    /** @var \App\Support\Console\Cli */
    protected $cli;

    /** @var CliCommandFactory */
    protected $dockerCompose;

    /** @var Porter */
    protected $porter;

    /** @var PorterLibrary */
    protected $porterLibrary;

    public function __construct(
        Cli $cli,
        CliCommandFactory $dockerCompose,
        Porter $porter,
        PorterLibrary $porterLibrary)
    {
        parent::__construct();

        $this->cli = $cli;
        $this->dockerCompose = $dockerCompose;
        $this->porter = $porter;
        $this->porterLibrary = $porterLibrary;
    }
}
