<?php

namespace Tests;

use App\PorterLibrary;
use App\Support\FilePublisher;
use App\Support\Mechanics\Mechanic;
use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application and returns it.
     *
     * @throws \App\Exceptions\PorterSetupFailed
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $files = new FileSystem();
        $files->deleteDirectory(storage_path('test_library'));

        $lib = new PorterLibrary(new FilePublisher($files), app(Mechanic::class), storage_path('test_library'));
        $lib->dontMigrateAndSeedDatabase()->setUp($app);

        return $app;
    }
}
