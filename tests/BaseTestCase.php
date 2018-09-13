<?php

namespace Tests;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class BaseTestCase extends IlluminateTestCase
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

        $this->performMigrations();

        static::$migrated = true;
    }

    protected function performMigrations()
    {

    }
}
