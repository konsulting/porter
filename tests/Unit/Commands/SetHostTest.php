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
        $this->app->extend(Mechanic::class, function () {
            $mock = Mockery::mock(Mechanic::class);
            $mock->shouldReceive('setupNetworking')->withNoArgs()->once();
            $mock->shouldReceive('getHostAddress')->andReturn('1.1.1.1')->once();

            return $mock;
        });

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
        $this->app->extend(Mechanic::class, function () {
            $mock = Mockery::mock(Mechanic::class);
            $mock->shouldReceive('restoreNetworking')->withNoArgs()->once();

            return $mock;
        });

        $this->app->extend(Config::class, function () {
            return Mockery::mock(Config::class)
                ->shouldReceive('updateIp')->with('127.0.0.1')->once()
                ->getMock();
        });

        $this->artisan('dns:set-host', ['--restore' => true]);
    }
}
