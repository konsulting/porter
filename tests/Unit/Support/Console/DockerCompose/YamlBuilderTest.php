<?php

namespace Tests\Unit\Support\Console\DockerCompose;

use App\Models\Setting;
use App\PorterLibrary;
use App\Support\Console\DockerCompose\YamlBuilder;
use App\Support\Images\ImageRepository;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class YamlBuilderTest extends BaseTestCase
{
    /** @var Filesystem|MockInterface */
    protected $files;
    /** @var PorterLibrary|MockInterface */
    private $lib;
    /** @var ImageRepository */
    private $images;
    /** @var YamlBuilder */
    private $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->files = \Mockery::mock(Filesystem::class);
        $this->lib = \Mockery::mock(PorterLibrary::class);
        $this->images = new ImageRepository(resource_path('image_sets/konsulting/porter-ubuntu'));
        $this->builder = new YamlBuilder($this->files, $this->lib);
    }

    /** @test */
    public function it_will_build_a_yaml_file()
    {
        $this->lib->shouldReceive('path')->once()->andReturn('pathtodotporter');

        $output = $this->builder->renderDockerComposeFile($this->images);

        $this->assertContains('pathtodotporter', $output);
    }

    /** @test */
    public function it_will_build_a_yaml_file_with_all_the_services()
    {
        Setting::updateOrCreate('use_browser', 'on');
        Setting::updateOrCreate('use_dns', 'on');
        Setting::updateOrCreate('use_redis', 'on');
        Setting::updateOrCreate('use_mysql', 'on');

        $this->lib->shouldReceive('path')->once()->andReturn('pathtodotporter');

        $output = $this->builder->renderDockerComposeFile($this->images);

        $this->assertContains('browser:', $output);
        $this->assertContains('dns:', $output);
        $this->assertContains('redis:', $output);
        $this->assertContains('mysql:', $output);
        $this->assertContains('pathtodotporter', $output);
    }

    /** @test */
    public function it_will_build_a_yaml_file_without_all_the_services()
    {
        Setting::updateOrCreate('use_browser', 'off');
        Setting::updateOrCreate('use_dns', 'off');
        Setting::updateOrCreate('use_redis', 'off');
        Setting::updateOrCreate('use_mysql', 'off');

        $this->lib->shouldReceive('path')->once()->andReturn('pathtodotporter');

        $output = $this->builder->renderDockerComposeFile($this->images);

        $this->assertNotContains('browser:', $output);
        $this->assertNotContains('dns:', $output);
        $this->assertNotContains('redis:', $output);
        $this->assertNotContains('mysql:', $output);
        $this->assertContains('pathtodotporter', $output);
    }

    /** @test */
    public function it_will_build_a_yaml_file_dns_when_the_setting_doesnt_exist()
    {
        $this->lib->shouldReceive('path')->once()->andReturn('pathtodotporter');

        $output = $this->builder->renderDockerComposeFile($this->images);

        $this->assertContains('dns:', $output);
        $this->assertNull(setting('use_dns'));
    }

    /** @test */
    public function it_will_create_the_correct_file()
    {
        $this->lib->shouldReceive('path')->twice()->andReturn('pathtodotporter');
        $this->lib->shouldReceive('dockerComposeFile')->once()->andReturn('docker-compose.yaml');

        $content = $this->builder->renderDockerComposeFile($this->images);
        $this->files->shouldReceive('put')->with('docker-compose.yaml', $content)->once();

        $this->builder->build($this->images);
    }

    /** @test */
    public function it_will_remove_the_file()
    {
        $this->lib->shouldReceive('dockerComposeFile')->once()->andReturn('docker-compose.yaml');

        $this->files->shouldReceive('delete')->with('docker-compose.yaml')->once();

        $this->builder->destroy();
    }
}
