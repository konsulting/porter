<?php

namespace Tests;

use App\Porter;
use App\PorterLibrary;
use App\Support\Console\DockerCompose\CliCommandFactory;
use App\Support\Console\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageSetRepository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;
use Illuminate\Support\Facades\Artisan;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class BaseTestCase extends IlluminateTestCase
{
    use CreatesApplication;
    use MockeryPHPUnitIntegration;
    use DatabaseTransactions;

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

            $this->remakePorter();

            Artisan::call('migrate:fresh');

            $this->preparePorter();
        });
    }

    /**
     * Re-make Porter class for tests, since we adjusted the PorterLibrary.
     */
    protected function remakePorter()
    {
        $this->app->extend(Porter::class, function ($porter, $app) {
            return new Porter(
                $app[ImageSetRepository::class],
                $app[Cli::class],
                $app[CliCommandFactory::class],
                new YamlBuilder($app[Filesystem::class], $app[PorterLibrary::class])
            );
        });
    }

    /**
     * Prepare Porter for the test.
     */
    protected function preparePorter()
    {
        //
    }

    /**
     * Mock a Porter command, one that extends the App\Commands\BaseCommand.
     *
     * @param $class
     *
     * @return \Mockery\MockInterface
     */
    protected function mockPorterCommand($class)
    {
        $mock = \Mockery::mock(
            $class.'[handle]',
            [
                app(Cli::class),
                app(CliCommandFactory::class),
                app(Porter::class),
                app(PorterLibrary::class),
            ]
        );

        $mock->shouldReceive('handle');

        $this->app[Kernel::class]->registerCommand($mock);

        return $mock;
    }
}
