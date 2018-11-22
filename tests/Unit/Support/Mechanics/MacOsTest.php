<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\MacOs;

class MacOsTest extends MechanicTestCase
{
    protected $mechanicClass = MacOs::class;

    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->assertSame(
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

    /** @test */
    public function it_can_setup_networking()
    {
        $this->consoleWriter->shouldReceive('info')->once();

        $this->cli->shouldReceive('passthru')
            ->with('sudo ifconfig lo0 alias 10.200.10.1/24')
            ->once();

        $this->getMechanic()->setupNetworking();
    }

    /** @test */
    public function it_can_restore_networking()
    {
        $this->consoleWriter->shouldReceive('info')->once();

        $this->cli->shouldReceive('passthru')
            ->with('sudo ifconfig lo0 -alias 10.200.10.1')
            ->once();

        $this->getMechanic()->restoreNetworking();
    }

    /** @test */
    public function it_returns_the_host_address()
    {
        $this->assertEquals('10.200.10.1', $this->getMechanic()->getHostAddress());
    }
}
