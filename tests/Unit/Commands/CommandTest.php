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
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;


class CommandTest extends BaseTestCase
{
    /** @test */
    public function it_checks_if_porter_has_been_set_up()
    {
        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('alreadySetUp')->andReturn(false);

        $this->expectException(PorterNotSetUp::class);

        $command = new SomeCommand(
            Mockery::spy(Cli::class),
            Mockery::spy(CliCommandFactory::class),
            Mockery::spy(Porter::class),
            $porterLibrary
        );

        $command->run(Mockery::spy(InputInterface::class), new ConsoleOutput);
    }
}

class SomeCommand extends BaseCommand
{
}
