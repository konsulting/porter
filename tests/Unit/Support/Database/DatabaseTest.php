<?php

namespace Tests\Unit\Support\Database;

use App\Support\Database\Database;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Kernel;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected $databasePath;

    /**
     * @var Kernel
     */
    protected $artisan;


    /** @test */
    public function it_creates_a_database_file_if_one_does_not_exist()
    {
        $this->databasePath = storage_path('test_library/testcreation.sqlite');
        $this->app->config['database.connections.default.database'] = $this->databasePath;

        $this->assertFileNotExists($this->databasePath);
        $this->assertFalse(Database::exists());

        Artisan::shouldReceive('call')->withArgs(['migrate:fresh']);
        Artisan::shouldReceive('call')->withArgs(['db:seed']);

        Database::ensureExists();

        $this->assertFileExists($this->databasePath);

        unlink($this->databasePath);
    }

    /** @test */
    public function it_can_force_refresh_the_database_if_it_already_exists()
    {
        $this->assertTrue(Database::exists());

        Artisan::shouldReceive('call')->withArgs(['migrate:fresh']);
        Artisan::shouldReceive('call')->withArgs(['db:seed']);

        Database::ensureExists(true);
    }

    /**
     * Store the kernel before the test so we can restore it after swapping for a mock.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan = Artisan::getFacadeRoot();
    }


    /**
     * Restore the artisan kernel so we can run artisan commands.
     *
     * @return void
     */
    protected function tearDown()
    {
        Artisan::swap($this->artisan);

        parent::tearDown();
    }


}
