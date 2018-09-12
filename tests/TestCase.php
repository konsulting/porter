<?php

namespace Tests;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration, DatabaseMigrations;

    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    protected static $migrated = false;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (static::$migrated) {
            return;
        }

        $this->app = $this->createApplication();

        $this->cleanseDir(storage_path('test_library'));
        @rmdir(storage_path('test_library'));

        mkdir(storage_path('test_library'));
        mkdir(storage_path('test_library/ssl'));
        mkdir(storage_path('test_library/config/nginx/conf.d/'), 0777, true);

        touch(storage_path('test_library') . '/testing.sqlite');

        $this->app['config']->set('database.connections.default.database', storage_path('test_library') . '/testing.sqlite');
        $this->app['config']->set('porter.docker-compose-file', storage_path('test_library/docker-compose.yaml'));
        $this->app['config']->set('porter.library_path', storage_path('test_library'));

        AppServiceProvider::$publishes[AppServiceProvider::class] = [
            resource_path('stubs/config') => config('porter.library_path') . '/config'
        ];

        Artisan::call('migrate:fresh');
        Artisan::call('vendor:publish', ['--provider' => AppServiceProvider::class]);

        static::$migrated = true;
    }

    protected function cleanseDir($dir)
    {
        if (! file_exists($dir)) {
            return;
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $current = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($current)) {
                $this->removeDir($current);
                continue;
            }

            unlink($current);
        }
    }

    protected function removeDir($dir)
    {
        if (! file_exists($dir)) {
            return true;
        }

        if (! is_dir($dir)) {
            throw new \Exception($dir . ' is not a directory');
        }

        $this->cleanseDir($dir);

        return rmdir($dir);
    }
}
