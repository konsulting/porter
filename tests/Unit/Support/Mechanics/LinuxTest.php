<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\Linux;

class LinuxTest extends MechanicTestCase
{
    protected $mechanicClass = Linux::class;

    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->assertEquals(
            '/Users/keoghan',
            $this->getMechanic(['HOME'=>'/Users/keoghan'])->getUserHomePath()
        );
    }
}
