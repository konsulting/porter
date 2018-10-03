<?php

namespace App\Commands;

use App\Exceptions\PorterNotSetUp;
use App\Porter;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\DockerCompose\CliCommandFactory;
use LaravelZero\Framework\Commands\Command;
use NunoMaduro\LaravelConsoleMenu\Menu;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * If true, the command will not run unless the setup process has been run.
     *
     * @var bool
     */
    protected $porterMustBeSetUp = true;

    public function __construct(
        Cli $cli,
        CliCommandFactory $dockerCompose,
        Porter $porter,
        PorterLibrary $porterLibrary
    ) {
        parent::__construct();

        $this->cli = $cli;
        $this->dockerCompose = $dockerCompose;
        $this->porter = $porter;
        $this->porterLibrary = $porterLibrary;
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     * @throws PorterNotSetUp
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkPorterIsSetUp();

        return parent::execute($input, $output);
    }


    /**
     * Ensure that Porter has been set up if necessary before continuing.
     *
     * @throws PorterNotSetUp
     */
    private function checkPorterIsSetUp()
    {
        if ($this->porterMustBeSetUp && ! $this->porterLibrary->alreadySetUp()) {
            throw new PorterNotSetUp('Porter must be set up to run this command. Run \'porter begin\' first.');
        }
    }
}
