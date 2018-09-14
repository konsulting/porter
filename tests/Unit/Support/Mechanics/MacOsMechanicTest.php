<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\MacOs;

class MacOsMechanicTest extends MechanicTestCase
{
    protected $mechanicClass = MacOs::class;

    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->assertEquals(
            '/Users/keoghan',
            $this->getMechanic(['HOME'=>'/Users/keoghan'])->getUserHomePath()
        );
    }

    /** @test */
    public function it_can_flush_the_dns()
    {
        $this->consoleWriter->shouldReceive('info')->once();

        $this->cli->shouldReceive('passthru')
            ->with('sudo killall -HUP mDNSResponder')
            ->once();

        $this->getMechanic()->flushDns();
    }
}
