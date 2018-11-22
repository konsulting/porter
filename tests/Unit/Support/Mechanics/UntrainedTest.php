<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\Untrained;

class UntrainedTest extends MechanicTestCase
{
    protected $mechanicClass = Untrained::class;

    /**
     * @dataProvider CommandProvider
     * @test
     */
    public function it_writes_to_console_for_every_command($method, ...$args)
    {
        $this->consoleWriter->shouldReceive('info')->atLeast()->once();

        $this->getMechanic()->$method(...$args);
    }

    public function commandProvider()
    {
        return [
            ['trustCA', ['']],
            ['trustCertificate', ['']],
            ['getUserHomePath'],
            ['flushDns'],
            ['setupNetworking'],
            ['restoreNetworking'],
        ];
    }

    /** @test */
    public function it_returns_the_host_address()
    {
        $this->assertEquals('127.0.0.1', $this->getMechanic()->getHostAddress());
    }
}
