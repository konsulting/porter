<?php

namespace Tests\Unit\Support\Images\Organiser;

use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use App\Support\Images\Image;
use App\Support\Images\Organiser\Organiser;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class OrganiserTest extends BaseTestCase
{
    /** @var Cli|MockInterface */
    protected $cli;

    /** @var Organiser */
    protected $organiser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cli = \Mockery::mock(Cli::class);
        $this->organiser = new Organiser(new TestImageRepository, $this->cli);
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
    public function it_pulls_the_docker_images()
    {
        $images = ['konsulting/php_cli', 'konsulting/php_fpm', 'another/node', 'another/mysql'];

        foreach ($images as $image) {
            $this->expectCommand('docker image inspect '.$image, 'exec')
                ->andReturn("Error: No such image: {$image}");

            $this->expectCommand('docker pull '.$image, 'passthru');
        }
        $this->organiser->pullImages();
    }

    /** @test */
    public function it_pulls_a_single_first_party_docker_image()
    {
        $this->expectCommand('docker image inspect konsulting/php_cli', 'exec')
            ->andReturn('Error: No such image: konsulting/php_cli');

        $this->expectCommand('docker pull konsulting/php_cli', 'passthru');

        $this->organiser->pullImages('php_cli');
    }

    /** @test */
    public function it_pulls_a_single_third_party_docker_image()
    {
        $this->expectCommand('docker image inspect another/node', 'exec')
            ->andReturn('Error: No such image: another/node');

        $this->expectCommand('docker pull another/node', 'passthru');

        $this->organiser->pullImages('node');
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
        $this->organiser->buildImages();
    }

    /** @test */
    public function it_builds_a_single_first_party_image()
    {
        $this->expectCommand('docker build -t konsulting/php_cli --rm php_cli_path --', 'passthru');
        $this->organiser->buildImages('php_cli');
    }

    /** @test */
    public function it_does_not_build_third_party_images()
    {
        $this->cli->shouldNotReceive('passthru');
        $this->organiser->buildImages('node');
    }

    /** @test */
    public function it_pushes_the_first_party_images()
    {
        foreach (['konsulting/php_cli', 'konsulting/php_fpm'] as $image) {
            $this->expectCommand('docker push '.$image, 'passthru');
        }
        $this->organiser->pushImages();
    }

    /** @test */
    public function it_pushes_a_single_first_party_image()
    {
        foreach (['konsulting/php_cli', 'konsulting/php_fpm'] as $image) {
            $this->expectCommand('docker push '.$image, 'passthru');
        }
        $this->organiser->pushImages();
    }

    /** @test */
    public function it_does_not_push_third_party_images()
    {
        $this->cli->shouldNotReceive('passthru');
        $this->organiser->pushImages('node');
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

    public function firstByServiceName($service, $firstPartyOnly = false)
    {
        return $this->findByServiceName($service, $firstPartyOnly)[0];
    }

    public function getDockerContext()
    {
        return './docker';
    }
}
