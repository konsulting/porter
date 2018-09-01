<?php

namespace Tests\Feature;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\CreatesApplication;

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

        $this->app['config']->set('porter.library_path', storage_path('test_library'));
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->cleanseDir(storage_path('test_library'));
        @rmdir(storage_path('test_library'));
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
