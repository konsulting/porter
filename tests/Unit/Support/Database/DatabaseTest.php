<?php

namespace Tests\Unit\Support\Database;

use App\Support\Database\Database;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanseDir(storage_path());

        $this->databasePath = storage_path('temp/test_database.sqlite');
        $this->app->config['database.connections.default.database'] = $this->databasePath;
    }

    protected function tearDown()
    {
        $this->cleanseDir(storage_path());
    }

    /** @test */
    public function it_creates_a_database_file_if_one_does_not_exist()
    {
        $this->assertFileNotExists($this->databasePath);
        $this->assertFalse(Database::exists());
        Database::ensureExists();
        $this->assertFileExists($this->databasePath);
        $this->assertTrue(Database::exists());

        // Check that migrations have been run
        $this->assertGreaterThan(0, DB::table('migrations')->count());
    }

    /** @test */
    public function it_can_force_refresh_the_database_if_it_already_exists()
    {
        if (! is_dir(dirname($this->databasePath))) {
            mkdir(dirname($this->databasePath), 0777, true);
        }

        touch($this->databasePath);
        Database::ensureExists(true);

        $this->assertNotEmpty(file_get_contents($this->databasePath));
    }
}
