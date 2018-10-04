<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Porter;
use App\PorterLibrary;
use App\Support\Console\DockerCompose\CliCommandFactory;
use App\Support\Console\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use App\Support\Images\Image;
use Illuminate\Filesystem\Filesystem;
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
            new TestImageSetRepository(),
            $this->cli,
            new CliCommandFactory($this->cli),
            new YamlBuilder(new Filesystem(), app(PorterLibrary::class))
        );
        $this->composeFile = app(PorterLibrary::class)->dockerComposeFile();
    }

    /**
     * Set the command expectation.
     *
     * @param array|string $commands
     * @param string       $method
     *
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
    public function it_restarts_serving()
    {
        factory(PhpVersion::class)->create([
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
    public function it_pulls_the_docker_images()
    {
        $images = ['konsulting/php_cli', 'konsulting/php_fpm', 'another/node', 'another/mysql'];

        foreach ($images as $image) {
            $this->expectCommand('docker image inspect '.$image, 'exec')
                ->andReturn("Error: No such image: {$image}");

            $this->expectCommand('docker pull '.$image, 'passthru');
        }
        $this->porter->pullImages();
    }

    /** @test */
    public function it_pulls_a_single_first_party_docker_image()
    {
        $this->expectCommand('docker image inspect konsulting/php_cli', 'exec')
            ->andReturn('Error: No such image: konsulting/php_cli');

        $this->expectCommand('docker pull konsulting/php_cli', 'passthru');

        $this->porter->pullImages('php_cli');
    }

    /** @test */
    public function it_pulls_a_single_third_party_docker_image()
    {
        $this->expectCommand('docker image inspect another/node', 'exec')
            ->andReturn('Error: No such image: another/node');

        $this->expectCommand('docker pull another/node', 'passthru');

        $this->porter->pullImages('node');
    }

    /** @test */
    public function it_builds_the_first_party_images()
    {
        $images = [
            'php_cli_path' => 'konsulting/php_cli',
            'php_fpm_path' => 'konsulting/php_fpm',
        ];

        foreach ($images as $path => $image) {
            $this->expectCommand('docker build -t '.$image.' --rm '.$path.' --', 'passthru');
        }
        $this->porter->buildImages();
    }

    /** @test */
    public function it_builds_a_single_first_party_image()
    {
        $this->expectCommand('docker build -t konsulting/php_cli --rm php_cli_path --', 'passthru');
        $this->porter->buildImages('php_cli');
    }

    /** @test */
    public function it_does_not_build_third_party_images()
    {
        $this->cli->shouldNotReceive('passthru');
        $this->porter->buildImages('node');
    }

    /** @test */
    public function it_pushes_the_first_party_images()
    {
        foreach (['konsulting/php_cli', 'konsulting/php_fpm'] as $image) {
            $this->expectCommand('docker push '.$image, 'passthru');
        }
        $this->porter->pushImages();
    }

    /** @test */
    public function it_pushes_a_single_first_party_image()
    {
        foreach (['konsulting/php_cli', 'konsulting/php_fpm'] as $image) {
            $this->expectCommand('docker push '.$image, 'passthru');
        }
        $this->porter->pushImages();
    }

    /** @test */
    public function it_does_not_push_third_party_images()
    {
        $this->cli->shouldNotReceive('passthru');
        $this->porter->pushImages('node');
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

class TestImageSetRepository implements ImageSetRepositoryContract
{
    public function addLocation($location)
    {
        //
    }

    public function getImageRepository($imageSetName)
    {
        return new TestImageRepository();
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
        return [
            new Image('konsulting/php_cli', 'php_cli_path'),
            new Image('konsulting/php_fpm', 'php_fpm_path'),
        ];
    }

    public function thirdParty()
    {
        return [new Image('another/node'), new Image('another/mysql')];
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

    public function findByServiceName($service, $firstPartyOnly = false)
    {
        if ($service == 'php_cli') {
            return [new Image('konsulting/php_cli', 'php_cli_path')];
        }

        if ($service == 'node') {
            return $firstPartyOnly ? [] : [new Image('another/node')];
        }

        return $firstPartyOnly ? $this->firstParty() : $this->all();
    }

    public function getDockerContext()
    {
        return './docker';
    }
}
