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

    /** @var Filesystem */
    protected $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repoName = 'konsulting/porter-ubuntu';
        $this->path = storage_path('test_library/image-repo-test/'.$this->repoName);

        $this->fileSystem = new Filesystem();
        $this->fileSystem->makeDirectory($this->path.'/php_fpm_7-2', 0755, true);
        $this->fileSystem->makeDirectory($this->path.'/php_cli_7-2', 0755, true);
        $this->fileSystem->put($this->path.'/php_fpm_7-2/Dockerfile', '');
        $this->fileSystem->put($this->path.'/php_cli_7-2/Dockerfile', '');
        $this->fileSystem->put($this->path.'/config.json', $this->configStub());

        $this->repo = new ImageRepository($this->path);
    }

    protected function configStub()
    {
        return json_encode([
            'name'       => 'konsulting/porter-ubuntu',
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

    public function tearDown(): void
    {
        parent::tearDown();

        $fs = new Filesystem();
        $fs->deleteDirectory($this->path);
    }

    /** @test */
    public function it_finds_first_party_images()
    {
        $expectedImages = [
            new Image($this->repoName.'-php_cli_7-2:latest', $this->path.'/docker/php_cli_7-2'),
            new Image($this->repoName.'-php_fpm_7-2:latest', $this->path.'/docker/php_fpm_7-2'),
        ];

        $result = $this->repo->firstParty();

        foreach ($expectedImages as $i => $image) {
            /* @var Image $image */
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
            $this->repo->findByServiceName('php_fpm_7-2', $firstPartyOnly = true)[0]->getName()
        );
        $this->assertEmpty($this->repo->findByServiceName('mysql', $firstPartyonly = true));
    }

    /** @test */
    public function it_requires_a_config_json_to_be_present()
    {
        $this->fileSystem->delete($this->path.'/config.json');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Failed loading config for image set/');
        $this->repo = new ImageRepository($this->path);
    }

    /** @test */
    public function it_requires_the_name_to_be_specified_in_config_json()
    {
        $this->fileSystem->delete($this->path.'/config.json');
        $this->fileSystem->put($this->path.'/config.json', '{}');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/There is no name specified/');

        $this->repo = new ImageRepository($this->path);
    }

    /** @test */
    public function it_finds_the_first_image_from_a_service_name()
    {
        $this->assertEquals($this->repoName.'-php_fpm_7-2:latest', $this->repo->findByServiceName('php_fpm_7-2')[0]->getName());
        $this->assertEquals('mysql:5.7', $this->repo->firstByServiceName('mysql')->getName());
    }

    /** @test */
    public function it_doesnt_find_the_first_image_with_no_service_name()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/A service name must be provided/');
        $this->repo->firstByServiceName(null);
    }

    /** @test */
    public function it_finds_the_first_image_from_first_party_only_by_service_name()
    {
        $this->assertEquals(
            $this->repoName.'-php_fpm_7-2:latest',
            $this->repo->firstByServiceName('php_fpm_7-2', $firstPartyOnly = true)->getName()
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Service not found/');
        $this->repo->firstByServiceName('mysql', $firstPartyonly = true);
    }
}
