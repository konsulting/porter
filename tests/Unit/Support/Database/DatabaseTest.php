<?php

namespace Tests\Unit\Support\Database;

use App\Support\Database\Database;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        @mkdir(storage_path('temp'));
        $this->cleanseDir(storage_path('temp'));
    }

    public function tearDown()
    {
        $this->cleanseDir(storage_path('temp'));
    }

    /** @test */
    public function it_creates_a_database_file_if_one_does_not_exist()
    {
        $this->databasePath = storage_path('temp/testing.sqlite');
        $this->app->config['database.connections.default.database'] = $this->databasePath;

        $this->assertFileNotExists($this->databasePath);
        $this->assertFalse(Database::exists());

        Artisan::shouldReceive('call')->withArgs(['migrate:fresh']);
        Artisan::shouldReceive('call')->withArgs(['db:seed']);

        Database::ensureExists();

        $this->assertFileExists($this->databasePath);
    }

    /** @test */
    public function it_can_force_refresh_the_database_if_it_already_exists()
    {
        $this->assertTrue(Database::exists());

        Artisan::shouldReceive('call')->withArgs(['migrate:fresh']);
        Artisan::shouldReceive('call')->withArgs(['db:seed']);

        Database::ensureExists(true);
    }
}
