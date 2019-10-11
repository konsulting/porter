<?php

namespace Tests\Unit\Support\Console\DockerCompose;

use App\Models\Setting;
use App\Models\Site;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\DockerCompose\CliCommand;
use App\Support\Console\DockerCompose\CliCommandFactory;
use Tests\BaseTestCase;

class CliCommandFactoryTest extends BaseTestCase
{
    /** @var CliCommandFactory */
    protected $cf;
    /** @var Cli|\Mockery\MockInterface */
    protected $cli;

    public function setUp(): void
    {
        parent::setUp();

        $lib = \Mockery::mock(PorterLibrary::class);
        $this->app->instance(PorterLibrary::class, $lib);

        $lib->shouldReceive('dockerComposeFile')->andReturn('file')->once();

        $this->cli = \Mockery::mock(Cli::class);
        $this->app->instance(Cli::class, $this->cli);

        $this->cf = new CliCommandFactory($this->cli);
    }

    /** @test */
    public function it_will_create_a_cli_command()
    {
        $command = $this->cf->command('up');

        $this->assertInstanceOf(CliCommand::class, $command);
        $this->assertSame('docker-compose -f file -p porter up', $command->prepare());
    }

    /** @test */
    public function it_will_create_an_exec_container_command()
    {
        $command = $this->cf->execContainer('mysql');

        $this->assertInstanceOf(CliCommand::class, $command);
        $this->assertSame('docker-compose -f file -p porter exec mysql', $command->prepare());
    }

    /** @test */
    public function it_will_create_a_run_container_command()
    {
        Setting::updateOrCreate('home', getcwd());
        $this->cli->shouldReceive('currentWorkingDirectory')->andReturn('/home');
        $command = $this->cf->runContainer('mysql');

        $this->assertInstanceOf(CliCommand::class, $command);
        $this->assertSame('docker-compose -f file -p porter run --rm --service-ports mysql',
            $command->prepare());
    }

    /** @test */
    public function it_will_create_a_run_container_command_for_a_specific_site()
    {
        factory(Site::class)->create(['name' => 'site']);
        Setting::updateOrCreate('home', '/home');
        $this->cli->shouldReceive('currentWorkingDirectory')->andReturn('/home/site');
        $command = $this->cf->runContainer('mysql');

        $this->assertInstanceOf(CliCommand::class, $command);
        $this->assertSame('docker-compose -f file -p porter run -w /srv/app/site --rm --service-ports mysql',
            $command->prepare());
    }
}
