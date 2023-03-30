<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Porter;
use App\PorterLibrary;
use App\Support\Console\DockerCompose\CliCommandFactory;
use App\Support\Console\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Images\ImageSetRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class PorterTest extends BaseTestCase
{
    /** @var Porter */
    protected $porter;

    /** @var string */
    protected $composeFile;

    /** @var Cli|MockInterface */
    protected $cli;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cli = \Mockery::mock(Cli::class);

        $this->porter = new Porter(
            new ImageSetRepository([
                resource_path('image_sets'),
            ]),
            $this->cli,
            new CliCommandFactory($this->cli),
            new YamlBuilder(new Filesystem(), app(PorterLibrary::class))
        );
        $this->composeFile = app(PorterLibrary::class)->dockerComposeFile();
    }

    /**
     * Set the command expectation.
     *
     * @param string       $method
     * @return \Mockery\Expectation
     */
    protected function expectCommand(array|string $commands, $method = 'exec')
    {
        return $this->cli->shouldReceive($method)
            ->withArgs((array) $commands)
            ->once();
    }

    /** @test */
    public function it_builds_the_docker_compose_yaml()
    {
        $this->porter->compose();
        $this->assertFileExists($this->composeFile);
    }

    /** @test */
    public function it_checks_if_porter_containers_are_running()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps');
        $this->assertFalse($this->porter->isUp('service'));
    }

    /** @test */
    public function it_starts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --remove-orphans', 'execRealTime');
        $this->porter->start();
    }

    /** @test */
    public function it_stops_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter down --remove-orphans', 'execRealTime');
        $this->porter->stop();
    }

    /** @test */
    public function it_restarts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps');
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --force-recreate --remove-orphans service', 'execRealTime');
        $this->porter->restart('service');

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps');
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --force-recreate --remove-orphans myService', 'execRealTime');
        $this->porter->restart('myService');
    }

    /** @test */
    public function it_soft_restarts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter restart', 'execRealTime');
        $this->porter->softRestart();

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter restart myService', 'execRealTime');
        $this->porter->softRestart('myService');
    }

    /** @test */
    public function it_restarts_serving()
    {
        Event::fake();

        PhpVersion::factory()->create([
            'version_number' => '7.2',
            'default'        => true,
        ]);

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps')
            ->times(3)
            ->andReturn('porter_', '', '');

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --remove-orphans php_fpm_7-2', 'execRealTime');
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --remove-orphans php_cli_7-2', 'execRealTime');
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --force-recreate --remove-orphans nginx', 'execRealTime');

        $this->porter->restartServing();
    }

    /** @test */
    public function it_builds_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter build');
        $this->porter->build();
    }

    /** @test */
    public function it_turns_a_service_on_if_it_is_off()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps')
            ->andReturn('porter_');

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter up -d --remove-orphans browser', 'execRealTime');

        $this->porter->turnOnService('browser');

        $this->assertEquals('on', Setting::where('name', 'use_browser')->value('value'));
    }

    /** @test */
    public function it_leaves_a_service_on_if_it_is_on()
    {
        Setting::updateOrCreate('use_browser', 'on');

        $this->cli->shouldNotReceive('exec');

        $this->porter->turnOnService('browser');
    }

    /** @test */
    public function it_turns_a_service_off_if_it_is_on()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps')
            ->andReturn('porter_');

        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter stop browser', 'execRealTime');

        Setting::updateOrCreate('use_browser', 'on');

        $this->porter->turnOffService('browser');

        $this->assertEquals('off', Setting::where('name', 'use_browser')->value('value'));
    }

    /** @test */
    public function it_leaves_a_service_off_if_it_is_off()
    {
        $this->cli->shouldNotReceive('exec');

        $this->porter->turnOffService('browser');
    }

    /** @test */
    public function it_returns_the_status()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter ps');

        $this->porter->status();
    }

    /** @test */
    public function it_returns_the_logs()
    {
        $this->expectCommand('docker-compose -f '.$this->composeFile.' -p porter logs service');

        $this->porter->logs('service');
    }
}
