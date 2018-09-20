<?php

namespace Tests\Unit\Support\Images;

use App\Support\Images\Image;
use App\Support\Images\ImageRepository;
use Illuminate\Filesystem\Filesystem;
use Tests\BaseTestCase;

class ImageRepositoryTest extends BaseTestCase
{
    protected $path = '';
    protected $repoName = '';

    /** @var ImageRepository */
    protected $repo;

    protected function setUp() : void
    {
        parent::setUp();

        $this->repoName = 'konsulting/porter-ubuntu';
        $this->path = storage_path('test_library/image-repo-test/'.$this->repoName);

        $fs = new Filesystem();
        $fs->makeDirectory($this->path.'/php_fpm_7-2', 0755, true);
        $fs->makeDirectory($this->path.'/php_cli_7-2', 0755, true);
        $fs->put($this->path.'/php_fpm_7-2/Dockerfile', '');
        $fs->put($this->path.'/php_cli_7-2/Dockerfile', '');

        $this->repo = new ImageRepository($this->path, $this->repoName);
    }

    public function tearDown()
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->deleteDirectory($this->path);
    }

    /** @test */
    public function it_finds_first_party_images()
    {
        $expectedImages = [
            new Image($this->repoName.'-php_cli_7-2:latest', $this->path.'/php_cli_7-2'),
            new Image($this->repoName.'-php_fpm_7-2:latest', $this->path.'/php_fpm_7-2'),
        ];

        $result = $this->repo->firstParty();

        foreach ($expectedImages as $i => $image) {
            /** @var Image $image */
            $this->assertEquals($image->getName(), $result[$i]->getName());
            $this->assertEquals($image->getLocalPath(), $result[$i]->getLocalPath());
        }
    }

    /** @test */
    public function it_returns_the_third_party_images()
    {
        $this->assertCount(4, $this->repo->thirdParty());
    }

    /** @test */
    public function it_returns_all_the_images()
    {
        $this->assertCount(6, $this->repo->all());
    }

    /** @test */
    public function we_can_get_the_path_and_name_of_the_repo()
    {
        $this->assertEquals($this->path, $this->repo->getPath());
        $this->assertEquals($this->repoName, $this->repo->getName());
    }

    /** @test */
    public function it_finds_an_image_from_a_service_name()
    {
        $this->assertEquals($this->repoName.'-php_fpm_7-2:latest', $this->repo->findByServiceName('php_fpm_7-2')[0]->getName());
        $this->assertEquals('mysql:5.7', $this->repo->findByServiceName('mysql')[0]->getName());
    }

    /** @test */
    public function it_finds_an_image_from_first_party_only_by_service_name()
    {
        $this->assertEquals(
            $this->repoName.'-php_fpm_7-2:latest',
            $this->repo->findByServiceName('php_fpm_7-2', $firstParty = true)[0]->getName()
        );
        $this->assertEmpty($this->repo->findByServiceName('mysql', $firstParty = true));
    }
}
