<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

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
    }

    protected function cleanseDir($dir)
    {
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $current = $dir.DIRECTORY_SEPARATOR.$item;

            if (is_dir($current)) {
                $this->removeDir($current);
                continue;
            }

            unlink($current);
        }
    }

    protected function removeDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            throw new \Exception($dir.' is not a directory');
        }

        $this->cleanseDir($dir);

        return rmdir($dir);
    }
}
