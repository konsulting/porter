<?php

namespace Tests;

use App\Providers\AppServiceProvider;
use Faker\Provider\File;
use Illuminate\Filesystem\Filesystem;
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

        return $app;
    }

    /**
     * Setup local storage for running the tests
     */
    public function setupStorage()
    {
        $files = new FileSystem;

        $files->deleteDirectory(storage_path('test_library'));

        $files->makeDirectory(storage_path('test_library/ssl'), 0755, true);
        $files->makeDirectory(storage_path('test_library/config/nginx/conf.d/'), 0755, true);

        $files->put(storage_path('test_library') . '/testing.sqlite', '');
    }
}
