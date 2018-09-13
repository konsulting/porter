<?php

namespace Tests;

use App\Providers\AppServiceProvider;
use LaravelZero\Framework\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application and returns it.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->setupStorage();

        $app['config']->set('database.connections.default.database', storage_path('test_library') . '/testing.sqlite');
        $app['config']->set('porter.docker-compose-file', storage_path('test_library/docker-compose.yaml'));
        $app['config']->set('porter.library_path', storage_path('test_library'));

        AppServiceProvider::$publishes[AppServiceProvider::class] = [
            resource_path('stubs/config') => config('porter.library_path') . '/config'
        ];

        return $app;
    }

    /**
     * Setup local storage for running the tests
     */
    public function setupStorage()
    {
        $this->removeDir(storage_path('test_library'));

        mkdir(storage_path('test_library'));
        mkdir(storage_path('test_library/ssl'));
        mkdir(storage_path('test_library/config/nginx/conf.d/'), 0777, true);

        touch(storage_path('test_library') . '/testing.sqlite');
    }

    /**
     * Clean out the contents of a directory recursively
     *
     * @param $dir
     * @throws \Exception
     */
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

    /**
     * Remove a directory
     *
     * @param $dir
     * @return bool
     * @throws \Exception
     */
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
