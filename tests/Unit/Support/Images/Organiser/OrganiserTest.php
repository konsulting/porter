<?php

namespace Tests\Unit\Support\Images\Organiser;

use App\Support\Contracts\Cli;
use App\Support\Images\ImageRepository;
use App\Support\Images\Organiser\Organiser;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class OrganiserTest extends BaseTestCase
{
    /** @var Cli|MockInterface */
    protected $cli;

    /** @var Organiser */
    protected $organiser;

    /** @var ImageRepository */
    protected $imageRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cli = \Mockery::mock(Cli::class);
        $this->imageRepo = new ImageRepository(base_path('tests/stubs/image_sets/test/repo'));

        $this->organiser = new Organiser(
            $this->imageRepo,
            $this->cli
        );
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
        $images = ['test/repo-php_cli:1.0.0', 'test/repo-php_fpm:1.0.0', 'another/node', 'another/mysql'];

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
        $this->expectCommand('docker image inspect test/repo-php_cli:1.0.0', 'exec')
            ->andReturn('Error: No such image: test/repo-php_cli:1.0.0');

        $this->expectCommand('docker pull test/repo-php_cli:1.0.0', 'passthru');

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
            $this->imageRepo->getDockerContext().'php_cli' => 'test/repo-php_cli:1.0.0',
            $this->imageRepo->getDockerContext().'php_fpm' => 'test/repo-php_fpm:1.0.0',
        ];

        foreach ($images as $path => $image) {
            $this->expectCommand('docker build -t '.$image.' --rm '.$path.' --', 'passthru');
        }
        $this->organiser->buildImages();
    }

    /** @test */
    public function it_builds_a_single_first_party_image()
    {
        $cliPath = $this->imageRepo->getDockerContext().'php_cli';

        $this->expectCommand("docker build -t test/repo-php_cli:1.0.0 --rm {$cliPath} --", 'passthru');
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
        foreach (['test/repo-php_cli:1.0.0', 'test/repo-php_fpm:1.0.0'] as $image) {
            $this->expectCommand('docker push '.$image, 'passthru');
        }
        $this->organiser->pushImages();
    }

    /** @test */
    public function it_pushes_a_single_first_party_image()
    {
        foreach (['test/repo-php_cli:1.0.0', 'test/repo-php_fpm:1.0.0'] as $image) {
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
