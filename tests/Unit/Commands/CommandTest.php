<?php

namespace Tests\Unit\Commands;

use App\Commands\BaseCommand;
use App\Exceptions\PorterNotSetUp;
use App\Porter;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\DockerCompose\CliCommandFactory;
use Mockery;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\BaseTestCase;

class CommandTest extends BaseTestCase
{
    /** @test */
    public function it_checks_if_porter_has_been_set_up()
    {
        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('alreadySetUp')->once()->andReturn(false);

        $this->expectException(PorterNotSetUp::class);

        $command = new MustHaveBeenSetUp(
            Mockery::spy(Cli::class),
            Mockery::spy(CliCommandFactory::class),
            Mockery::spy(Porter::class),
            $porterLibrary
        );

        $command->run(Mockery::spy(InputInterface::class), new ConsoleOutput());
    }

    /** @test */
    public function commands_can_be_run_before_setup()
    {
        $command = new CanRunBeforeSetup(
            Mockery::spy(Cli::class),
            Mockery::spy(CliCommandFactory::class),
            Mockery::spy(Porter::class),
            Mockery::spy(PorterLibrary::class)
        );
        $command->setLaravel(new \Illuminate\Container\Container());

        $input = Mockery::spy(InputInterface::class);
        $command->run($input, new ConsoleOutput());

        // Check that the handle() method is executed
        $input->shouldHaveReceived('getOptions');
    }
}

class MustHaveBeenSetUp extends BaseCommand
{
}

class CanRunBeforeSetup extends BaseCommand
{
    protected $porterMustBeSetUp = false;

    public function handle(): void
    {
        $this->input->getOptions();
    }
}
