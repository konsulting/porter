<?php

namespace Tests\Unit;

use App\PorterLibrary;
use App\Support\FilePublisher;
use App\Support\Mechanics\Mechanic;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Mockery\Mock;
use Tests\BaseTestCase;

class PorterLibraryTest extends BaseTestCase
{
    /**
     * @var Mock
     */
    protected $files;

    /**
     * @var Mock|FilePublisher
     */
    protected $filePublisher;

    /** @var PorterLibrary */
    protected $lib;

    /** @var Mock|Mechanic */
    protected $mechanic;

    public function setUp(): void
    {
        parent::setUp();

        $this->files = Mockery::mock(Filesystem::class);
        $this->filePublisher = Mockery::mock(FilePublisher::class);
        $this->mechanic = Mockery::mock(Mechanic::class);

        $this->filePublisher
            ->shouldReceive('getFileSystem')
            ->andReturn($this->files);
    }

    /**
     * Make the porter library.
     *
     * @param string $path
     *
     * @return PorterLibrary
     */
    protected function makeLibrary($path, $filePublisher = null, $mechanic = null)
    {
        return new PorterLibrary($filePublisher ?: $this->filePublisher,
            $mechanic ?: $this->mechanic, $path);
    }

    /** @test */
    public function it_presents_the_paths_we_need()
    {
        $lib = $this->makeLibrary('/Users/test/.porter');

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
        $lib = $this->makeLibrary('/Users/test/.porter');

        $this->files->shouldReceive('exists')->with('/Users/test/.porter')->andReturn(true);

        $this->assertTrue($lib->alreadySetUp());
    }

    /** @test */
    public function it_will_set_up_the_library()
    {
        Carbon::setTestNow('2018-01-01 00:00:00');
        $this->mechanic->shouldReceive('getUserHomePath')->withNoArgs()
            ->andReturn('/Users/test');

        $lib = $this->makeLibrary('');

        $this->files->shouldReceive('exists')
            ->with('/Users/test/.porter')
            ->andReturn(true)
            ->once();

        // Check we're backing up the existing directory
        $this->files->shouldReceive('moveDirectory')
            ->with('/Users/test/.porter', '/Users/test/.porter_20180101000000')
            ->once();

        // Publish .env
        $this->filePublisher->shouldReceive('publish')
            ->with(base_path('.env.example'), base_path('.env'))->once();

        $this->files->shouldReceive('get')->with(base_path('.env'))
            ->andReturn("LIBRARY_PATH=\n")->once();
        $this->files->shouldReceive('put')
            ->with(base_path('.env'), "LIBRARY_PATH=\"/Users/test/.porter\"\n")
            ->once();

        // Create database
        $this->files->shouldReceive('put')
            ->with('/Users/test/.porter/database.sqlite', '')->once();

        // Make directory structure
        $this->files->shouldReceive('isDirectory')->with('/Users/test/.porter/ssl')
            ->andReturn(false)->once();
        $this->files->shouldReceive('makeDirectory')
            ->with('/Users/test/.porter/ssl', 0755, $recursive = true)->once();
        $this->files->shouldReceive('isDirectory')->with('/Users/test/.porter/views/nginx')
            ->andReturn(false)->once();
        $this->files->shouldReceive('makeDirectory')
            ->with('/Users/test/.porter/views/nginx', 0755, $recursive = true)->once();

        // Publish config
        $this->filePublisher->shouldReceive('publish')
            ->with(resource_path('stubs/config'), '/Users/test/.porter/config')->once();

        $artisan = Artisan::spy();

        $lib->setUp($this->app, true);

        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');
        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');

        $artisan->shouldHaveReceived('call')->with('migrate:fresh');
        $artisan->shouldHaveReceived('call')->with('db:seed');

        $this->assertEquals($this->app[PorterLibrary::class], $lib);
    }

    /** @test */
    public function it_wont_migrate_when_asked_not_to()
    {
        Carbon::setTestNow('2018-01-01 00:00:00');

        $mechanic = Mockery::spy(Mechanic::class);
        $files = Mockery::spy(Filesystem::class);
        $filePublisher = Mockery::spy(FilePublisher::class);
        $filePublisher->shouldReceive('getFilesystem')->andReturn($files);

        $lib = $this->makeLibrary('/Users/test/.porter', $filePublisher, $mechanic);
        $lib->dontMigrateAndSeedDatabase();

        $artisan = Artisan::spy();

        $lib->setUp($this->app);

        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');
        $this->assertEquals(config('database.connections.default.database'), '/Users/test/.porter/database.sqlite');

        $artisan->shouldNotHaveReceived('call');

        $this->assertEquals($this->app[PorterLibrary::class], $lib);
    }
}
