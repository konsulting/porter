<?php

namespace Tests\Unit\Support\Images;

use App\Support\Images\ImageRepository;
use App\Support\Images\ImageSetRepository;
use Illuminate\Filesystem\Filesystem;
use Tests\BaseTestCase;

class ImageSetRepositoryTest extends BaseTestCase
{
    protected $path = '';
    protected $secondaryPath = '';

    /** @var ImageSetRepository */
    protected $repo;

    protected function setUp() : void
    {
        parent::setUp();

        $this->path = storage_path('test_library/image-set-repo-test');
        $this->secondaryPath = storage_path('test_library/image-set-repo-test-two');

        $fs = new Filesystem();

        foreach (['konsulting/porter-ubuntu', 'konsulting/porter-alpine'] as $imageSet) {
            $fs->makeDirectory($this->path.'/'.$imageSet.'/php_fpm_7-2', 0755, true);
            $fs->makeDirectory($this->path.'/'.$imageSet.'/php_cli_7-2', 0755, true);
            $fs->put($this->path.'/'.$imageSet.'/php_fpm_7-2/Dockerfile', '');
            $fs->put($this->path.'/'.$imageSet.'/php_cli_7-2/Dockerfile', '');
            $fs->put($this->path.'/'.$imageSet.'/config.json', $this->configStub($imageSet));
        }

        foreach (['konsulting/porter-custom'] as $imageSet) {
            $fs->makeDirectory($this->secondaryPath.'/'.$imageSet.'/php_fpm_7-2', 0755, true);
            $fs->makeDirectory($this->secondaryPath.'/'.$imageSet.'/php_cli_7-2', 0755, true);
            $fs->put($this->secondaryPath.'/'.$imageSet.'/php_fpm_7-2/Dockerfile', '');
            $fs->put($this->secondaryPath.'/'.$imageSet.'/php_cli_7-2/Dockerfile', '');
            $fs->put($this->secondaryPath.'/'.$imageSet.'/config.json', $this->configStub($imageSet));
        }

        $this->repo = new ImageSetRepository($this->path);
    }

    protected function configStub($name)
    {
        return json_encode([
            'name'       => $name,
            'firstParty' => [
                'php_cli_7-2' => 'latest',
                'php_fpm_7-2' => 'latest',
            ],
            'thirdParty' => [
                'mysql'   => 'mysql:5.7',
                'redis'   => 'redis:alpine',
                'dns'     => 'andyshinn/dnsmasq',
                'mailhog' => 'mailhog/mailhog:v1.0.0',
            ],
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->deleteDirectory($this->path);
    }

    /** @test */
    public function it_returns_a_base_image_repo()
    {
        $imageRepo = $this->repo->getImageRepository('konsulting/porter-ubuntu');

        $this->assertInstanceOf(ImageRepository::class, $imageRepo);
        $this->assertEquals('konsulting/porter-ubuntu', $imageRepo->getName());
    }

    /** @test */
    public function it_returns_a_secondary_image_repo()
    {
        $this->repo->addLocation($this->secondaryPath);
        $imageRepo = $this->repo->getImageRepository('konsulting/porter-custom');

        $this->assertInstanceOf(ImageRepository::class, $imageRepo);
        $this->assertEquals('konsulting/porter-custom', $imageRepo->getName());
    }

    /** @test */
    public function it_returns_a_list_of_available_image_sets()
    {
        $this->repo->addLocation($this->secondaryPath);
        $this->assertEquals([
            $this->path.'/konsulting/porter-alpine'          => 'konsulting/porter-alpine',
            $this->path.'/konsulting/porter-ubuntu'          => 'konsulting/porter-ubuntu',
            $this->secondaryPath.'/konsulting/porter-custom' => 'konsulting/porter-custom',
        ], $this->repo->availableImageSets()->toArray());
    }
}
