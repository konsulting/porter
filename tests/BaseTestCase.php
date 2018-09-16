<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use Illuminate\Support\Facades\Artisan;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class BaseTestCase extends IlluminateTestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration, DatabaseTransactions;

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

        $this->afterApplicationCreated(function () {
            if (static::$migrated) {
                return;
            }

            Artisan::call('migrate:fresh');

            $this->preparePorter();
        });
    }

    protected function preparePorter()
    {
    }
}
