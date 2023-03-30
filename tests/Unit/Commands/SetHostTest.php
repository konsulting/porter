<?php

namespace Tests\Unit\Commands;

use App\PorterLibrary;
use App\Support\Dnsmasq\Config;
use App\Support\Mechanics\Mechanic;
use Mockery;
use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class SetHostTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_sets_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldReceive('addAlternativeLoopbackAddress')->withNoArgs()->once();
        $mechanicMock->shouldReceive('getAlternativeLoopback')->andReturn('1.1.1.1')->once();

        // Mechanic is being fetched from the container more than once, so we need to bind a specific instance rather
        // than a closure
        $this->app->instance(Mechanic::class, $mechanicMock);
        $this->app->get(PorterLibrary::class)->setMechanic($mechanicMock);

        $this->app->extend(Config::class, fn() => Mockery::mock(Config::class)
            ->shouldReceive('updateIp')->with('1.1.1.1')->once()
            ->getMock());

        $this->porter->shouldReceive('restart')->with('dns')->once();

        $this->artisan('dns:set-host');
    }

    /** @test */
    public function it_restores_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldReceive('removeAlternativeLoopbackAddress')->withNoArgs()->once();
        $mechanicMock->shouldReceive('getStandardLoopback')->withNoArgs()->andReturn('127.0.0.1')->once();

        $this->app->instance(Mechanic::class, $mechanicMock);
        $this->app->get(PorterLibrary::class)->setMechanic($mechanicMock);

        $this->app->extend(Config::class, fn() => Mockery::mock(Config::class)
            ->shouldReceive('updateIp')->with('127.0.0.1')->once()
            ->getMock());

        $this->porter->shouldReceive('restart')->with('dns')->once();

        $this->artisan('dns:set-host', ['--restore' => true]);
    }
}
