<?php

namespace Tests\Unit;

use App\DockerCompose\CliCommandFactory;
use App\DockerCompose\YamlBuilder;
use App\Porter;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use App\Support\Images\Image;
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

        $this->porter = new Porter(
            new TestImageSetRepository,
            $this->cli,
            new CliCommandFactory($this->cli),
            new YamlBuilder
        );
        $this->composeFile = config('porter.docker-compose-file');
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
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter ps');
        $this->assertFalse($this->porter->isUp('service'));
    }

    /** @test */
    public function it_starts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter up -d --remove-orphans', 'execRealTime');
        $this->porter->start();
    }

    /** @test */
    public function it_stops_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter down --remove-orphans', 'execRealTime');
        $this->porter->stop();
    }

    /** @test */
    public function it_restarts_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter ps');
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter up -d --force-recreate --remove-orphans service', 'execRealTime');
        $this->porter->restart('service');

        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter ps');
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter up -d --force-recreate --remove-orphans myService', 'execRealTime');
        $this->porter->restart('myService');
    }

    /** @test */
    public function it_builds_the_porter_containers()
    {
        $this->expectCommand('docker-compose -f ' . $this->composeFile . ' -p porter build');
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

class TestImageSetRepository implements ImageSetRepositoryContract
{
    public function addLocation($location)
    {
        //
    }

    public function getImageRepository($imageSetName)
    {
        return new TestImageRepository;
    }

    public function availableImageSets()
    {
        return ['konsulting'];
    }
}

class TestImageRepository implements ImageRepositoryContract
{
    public function firstParty()
    {
        return [new Image('konsulting/image'), new Image('konsulting/image2')];
    }

    public function thirdParty()
    {
        return [new Image('another/image'), new Image('another/image2')];
    }

    public function all()
    {
        return array_merge($this->firstParty(), $this->thirdParty());
    }

    public function getPath()
    {
        return base_path('docker');
    }

    public function getName()
    {
        return 'konsulting/porter-ubuntu';
    }
}
