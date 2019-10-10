<?php

namespace Tests\Unit\Commands\Dns;

use Mockery;
use App\PorterLibrary;
use Tests\BaseTestCase;
use App\Support\Mechanics\Mechanic;

class Flush extends BaseTestCase
{
    /** @test */
    public function it_will_flush_the_dns()
    {
        $mechanicMock = Mockery::mock(Mechanic::class);
        $mechanicMock
            ->shouldIgnoreMissing()
            ->shouldReceive('flushDns')->withNoArgs()->once();

        $this->app->instance(Mechanic::class, $mechanicMock);
        $this->app->get(PorterLibrary::class)->setMechanic($mechanicMock);

        $this->artisan('dns:flush');
    }
}
