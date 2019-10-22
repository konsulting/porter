<?php

namespace Tests\Unit\Support\Console\DockerCompose;

use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\DockerCompose\CliCommand;
use Tests\BaseTestCase;

class CliCommandTest extends BaseTestCase
{
    /** @var Cli|\Mockery\MockInterface */
    protected $cli;
    /** @var PorterLibrary|\Mockery\MockInterface */
    protected $lib;

    public function setUp(): void
    {
        parent::setUp();

        $this->lib = \Mockery::mock(PorterLibrary::class);
        $this->app->instance(PorterLibrary::class, $this->lib);

        $this->cli = \Mockery::mock(Cli::class);
        $this->app->instance(Cli::class, $this->cli);
    }

    /** @test */
    public function it_forms_a_docker_compose_command()
    {
        $this->lib->shouldReceive('dockerComposeFile')->andReturn('file')->once();

        $command = new CliCommand($this->cli, 'up');

        $this->assertSame('docker-compose -f file -p porter up', $command->prepare());
    }

    /** @test */
    public function it_can_append_a_bash_command()
    {
        $this->lib->shouldReceive('dockerComposeFile')->andReturn('file')->once();

        $command = (new CliCommand($this->cli, 'node'))->bash();

        $this->assertSame('docker-compose -f file -p porter node bash', $command->prepare());
    }

    /** @test */
    public function appending_bash_makes_it_interactive()
    {
        $command = (new CliCommand($this->cli, 'node'));
        $command->bash();
        $this->assertTrue($command->isInteractive());
    }

    /** @test */
    public function it_can_append_a_bash_command_with_a_specified_command()
    {
        $this->lib->shouldReceive('dockerComposeFile')->andReturn('file')->once();

        $command = (new CliCommand($this->cli, 'node'))->bash('ls');

        $this->assertSame('docker-compose -f file -p porter node bash -c "ls"', $command->prepare());
    }

    /** @test */
    public function it_can_append_any_command()
    {
        $this->lib->shouldReceive('dockerComposeFile')->andReturn('file')->once();

        $command = (new CliCommand($this->cli, 'node'))->append(123);

        $this->assertSame('docker-compose -f file -p porter node 123', $command->prepare());
    }

    /** @test */
    public function it_can_be_made_interactive_or_not()
    {
        $command = (new CliCommand($this->cli, 'node'));

        $command->interactive();
        $this->assertTrue($command->isInteractive());

        $command->notInteractive();
        $this->assertFalse($command->isInteractive());
    }

    /** @test */
    public function it_can_be_made_real_time_or_not()
    {
        $command = (new CliCommand($this->cli, 'node'));

        $command->realTime();
        $this->assertTrue($command->isRealTime());

        $command->notRealTime();
        $this->assertFalse($command->isRealTime());
    }

    /** @test */
    public function perform_selects_the_correct_cli_procedure()
    {
        $this->lib->shouldReceive('dockerComposeFile')->andReturn('file')->times(3);
        $command = (new CliCommand($this->cli, 'node'));

        $this->cli->shouldReceive('exec')->once();
        $command->perform();

        $this->cli->shouldReceive('execRealTime')->once();
        $command->realTime();
        $command->perform();

        $this->cli->shouldReceive('passthru')->once();
        $command->notRealTime();
        $command->interactive();
        $command->perform();
    }

    /** @test */
    public function a_fresh_command_is_not_interactive_or_real_time()
    {
        $command = (new CliCommand($this->cli, 'node'));
        $this->assertFalse($command->isRealTime());
        $this->assertFalse($command->isInteractive());
    }

    /** @test */
    public function we_are_able_to_override_the_timeout_for_the_process()
    {
        // We want the actual Cli for this test.
        $this->app->forgetInstance(Cli::class);

        $command = new CliCommand(app()->make(Cli::class), 'node');

        $this->assertSame(config('porter.process_timeout'), $command->getCli()->getTimeout());

        $command->setTimeout(60);
        $this->assertSame(60, $command->getCli()->getTimeout());

        $command->doNotTimeout();
        $this->assertNull($command->getCli()->getTimeout());
    }
}
