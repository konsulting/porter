<?php

namespace Tests\Unit\Commands;

use App\Support\Images\Organiser\Organiser;
use App\Support\Mechanics\Mechanic;
use Tests\BaseTestCase;

class BeginTest extends BaseTestCase
{
    protected $organiser;

    public function setUp(): void
    {
        parent::setup();

        $this->organiser = \Mockery::mock(Organiser::class);
        $this->organiser->expects('pullImages');

        $this->afterApplicationCreated(function () {
            app()->bind(Organiser::class, fn() => $this->organiser);
        });
    }

    /** @test */
    public function it_uses_the_supplied_home_directory()
    {
        $this->mockMechanic();

        $home = storage_path('temp/test_home');
        file_exists($home) ? null : mkdir($home, 0777, true);

        $this->artisan('begin', ['--force' => true, 'home' => $home])
            ->expectsOutput("Setting home to {$home}");
    }

    /** @test */
    public function it_uses_the_current_directory_if_no_interaction()
    {
        $this->mockMechanic();

        $this->artisan('begin', ['--force' => true, '--no-interaction' => true])
            ->expectsOutput('Setting home to '.getcwd());
    }

    protected function mockMechanic()
    {
        $mechanic = \Mockery::mock(Mechanic::class);
        $mechanic->shouldReceive('trustCA')->once();

        $this->app->instance(Mechanic::class, $mechanic);
    }
}
