<?php

namespace Tests\Unit;

use App\DockerCompose\CliCommandFactory;
use App\Porter;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use Mockery\MockInterface;
use Tests\TestCase;

class PorterTest extends TestCase
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

        $this->porter = new Porter(new TestImageRepository, $this->cli, new CliCommandFactory($this->cli));
        $this->composeFile = config('app.docker-compose-file');
    }

    /**
     * Set the command expectation.
     *
     * @param array|string $commands
     * @param string       $method
     * @return \Mockery\Expectation
     */
    protected function expectCommand($commands, $method = 'exec')
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
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' ps');
        $this->assertFalse($this->porter->isUp('service'));
    }

    /** @test */
    public function it_starts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' up -d --remove-orphans', 'execRealTime');
        $this->porter->start();
    }

    /** @test */
    public function it_stops_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' down --remove-orphans', 'execRealTime');
        $this->porter->stop();
    }

    /** @test */
    public function it_restarts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' ps');
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' up -d --force-recreate --remove-orphans service', 'execRealTime');
        $this->porter->restart('service');

        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' ps');
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' up -d --force-recreate --remove-orphans myService', 'execRealTime');
        $this->porter->restart('myService');
    }

    /** @test */
    public function it_builds_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' build');
        $this->porter->build();
    }

    /** @test */
    public function it_pulls_the_docker_images()
    {
        $images = ['konsulting/image', 'konsulting/image2', 'another/image', 'another/image2'];
        foreach ($images as $image) {
            $this->cli->shouldReceive('passthru')->with('docker pull ' . $image)->once();
        }
        $this->porter->pullImages();
    }

    /** @test */
    public function it_pushes_the_first_party_images()
    {
        foreach (['konsulting/image', 'konsulting/image2'] as $image) {
            $this->cli->shouldReceive('passthru')->with('docker push ' . $image)->once();
        }
        $this->porter->pushImages();
    }
}

class TestImageRepository implements ImageRepositoryContract
{
    public function firstParty($imageSetName)
    {
        return ['konsulting/image', 'konsulting/image2'];
    }

    public function thirdParty()
    {
        return ['another/image', 'another/image2'];
    }
}
