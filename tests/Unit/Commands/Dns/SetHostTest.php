<?php

namespace Tests\Unit\Commands\Dns;

use Mockery;
use App\PorterLibrary;
use Tests\BaseTestCase;
use App\Support\Mechanics\Mechanic;
use Tests\Unit\Support\Concerns\MocksPorter;

class SetHostTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_will_set_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldIgnoreMissing();
        $mechanicMock->shouldReceive('addAlternativeLoopbackAddress')->once();
        $mechanicMock->shouldReceive('getAlternativeLoopback')->andReturn('1.1.1.1')->once();

        $this->app->instance(Mechanic::class, $mechanicMock);
        $this->app->get(PorterLibrary::class)->setMechanic($mechanicMock);

        $this->porter->shouldReceive('restart')->with('dns')->once();

        $this->artisan('dns:set-host');
    }

    /** @test */
    public function it_will_restore_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldIgnoreMissing();
        $mechanicMock->shouldReceive('removeAlternativeLoopbackAddress')->once();
        $mechanicMock->shouldReceive('getStandardLoopback')->andReturn('127.0.0.1')->once();

        $this->app->instance(Mechanic::class, $mechanicMock);
        $this->app->get(PorterLibrary::class)->setMechanic($mechanicMock);

        $this->porter->shouldReceive('restart')->with('dns')->once();

        $this->artisan('dns:set-host', ['--restore' => 1]);
    }
}
