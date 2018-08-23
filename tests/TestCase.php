<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration;

    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();

        touch(database_path('testing.sqlite'));

        @mkdir(storage_path('test_ssl'));
        @mkdir(storage_path('test_config/nginx/conf.d/'), 0777, true);

        $this->app['config']->set('database.connections.default.database', database_path('testing.sqlite'));
        $this->app['config']->set('app.docker-compose-file', storage_path('test_config/docker-compose-test.yaml'));
        $this->app['config']->set('app.config_storage_path', storage_path('test_config'));
        $this->app['config']->set('app.ssl_storage_path', storage_path('test_ssl'));

        Artisan::call('migrate:fresh');
        Artisan::call('vendor:publish', ['--provider' => TestingServiceProvider::class]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->cleanseDir(storage_path('test_ssl'));
        $this->cleanseDir(storage_path('test_config'));
    }

    protected function cleanseDir($dir)
    {
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
