<?php

namespace Tests\Unit\Commands;

use App\Support\Dnsmasq\Config;
use App\Support\Mechanics\Mechanic;
use Mockery;
use Tests\BaseTestCase;

class SetHostTest extends BaseTestCase
{
    /** @test */
    public function it_sets_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldReceive('setupNetworking')->withNoArgs()->once();
        $mechanicMock->shouldReceive('getHostAddress')->andReturn('1.1.1.1')->once();

        // Mechanic is being fetched from the container more than once, so we need to bind a specific instance rather
        // than a closure
        $this->app->instance(Mechanic::class, $mechanicMock);

        $this->app->extend(Config::class, function () {
            return Mockery::mock(Config::class)
                ->shouldReceive('updateIp')->with('1.1.1.1')->once()
                ->getMock();
        });

        $this->artisan('dns:set-host');
    }

    /** @test */
    public function it_restores_the_host()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock->shouldReceive('restoreNetworking')->withNoArgs()->once();

        $this->app->instance(Mechanic::class, $mechanicMock);

        $this->app->extend(Config::class, function () {
            return Mockery::mock(Config::class)
                ->shouldReceive('updateIp')->with('127.0.0.1')->once()
                ->getMock();
        });

        $this->artisan('dns:set-host', ['--restore' => true]);
    }
}
