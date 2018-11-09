<?php

namespace Tests\Unit;

use App\PorterLibrary;
use App\Support\FilePublisher;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Tests\BaseTestCase;

class PorterLibraryTest extends BaseTestCase
{
    protected $files;
    protected $filePublisher;

    /** @var PorterLibrary */
    protected $lib;

    public function setUp() : void
    {
        parent::setUp();

        $this->files = \Mockery::mock(Filesystem::class);
        $this->filePublisher = \Mockery::mock(FilePublisher::class);

        $this->filePublisher
            ->shouldReceive('getFileSystem')
            ->andReturn($this->files);
    }

    /** @test */
    public function it_presents_the_paths_we_need()
    {
        $lib = new PorterLibrary($this->filePublisher, '/Users/test/.porter');

        $this->assertEquals('/Users/test/.porter', $lib->path());
        $this->assertEquals('/Users/test/.porter/config', $lib->configPath());
        $this->assertEquals('/Users/test/.porter/database.sqlite', $lib->databaseFile());
        $this->assertEquals('/Users/test/.porter/docker-compose.yaml', $lib->dockerComposeFile());
        $this->assertEquals('/Users/test/.porter/image-sets', $lib->dockerImagesPath());
        $this->assertEquals('/Users/test/.porter/ssl', $lib->sslPath());
        $this->assertEquals('/Users/test/.porter/views', $lib->viewsPath());
    }

    /** @test */
    public function it_recognises_being_already_set_up()
    {
        $lib = new PorterLibrary($this->filePublisher, '/Users/test/.porter');

        $this->files->shouldReceive('exists')->with('/Users/test/.porter')->andReturn(true);

        $this->assertTrue($lib->alreadySetUp());
    }

    /** @test */
    public function it_will_set_up_the_library()
    {
        Carbon::setTestNow('2018-01-01 00:00:00');

        $lib = new PorterLibrary($this->filePublisher, '/Users/test/.porter');

        $this->files->shouldReceive('exists')
            ->with('/Users/test/.porter')
            ->andReturn(false);

        $this->files->shouldReceive('moveDirectory')
            ->with('/Users/test/.porter', '/Users/test/.porter_20180101000000');

        $this->filePublisher->shouldReceive('publish')
            ->with(base_path('.env.example'), base_path('.env'));

        $this->files->shouldReceive('get', base_path('.env'));
        $this->files->shouldReceive('put', base_path('.env'))
            ->with("LIBRARY_PATH=\"/Users/test/.porter\"\n");

        $this->files->shouldReceive('put', '/Users/test/.porter/database.sqlite');

        $this->filePublisher->shouldReceive('publish')
            ->with(resource_path('stubs/config'), '/Users/test/.porter/config');

        $lib->setUp($this->app);

        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');
        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');

        Artisan::spy()
            ->shouldReceive('migrate:fresh')
            ->shouldReceive('db:seed');

        $this->assertEquals($this->app[PorterLibrary::class], $lib);
    }

    /** @test */
    public function it_wont_migrate_when_asked_not_to()
    {
        Carbon::setTestNow('2018-01-01 00:00:00');

        $lib = new PorterLibrary($this->filePublisher, '/Users/test/.porter');
        $lib->dontMigrateAndSeedDatabase();

        $this->files->shouldReceive('exists')
            ->with('/Users/test/.porter')
            ->andReturn(false);

        $this->files->shouldReceive('moveDirectory')
            ->with('/Users/test/.porter', '/Users/test/.porter_20180101000000');

        $this->filePublisher->shouldReceive('publish')
            ->with(base_path('.env.example'), base_path('.env'));

        $this->files->shouldReceive('get', base_path('.env'));
        $this->files->shouldReceive('put', base_path('.env'))
            ->with("LIBRARY_PATH=\"/Users/test/.porter\"\n");

        $this->files->shouldReceive('put', '/Users/test/.porter/database.sqlite');

        $this->filePublisher->shouldReceive('publish')
            ->with(resource_path('stubs/config'), '/Users/test/.porter/config');

        $lib->setUp($this->app);

        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');
        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');

        Artisan::spy()
            ->shouldNotReceive('migrate:fresh')
            ->shouldNotReceive('db:seed');

        $this->assertEquals($this->app[PorterLibrary::class], $lib);
    }
}
