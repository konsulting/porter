<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\Windows;

class WindowsTest extends MechanicTestCase
{
    protected $mechanicClass = Windows::class;

    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->assertEquals(
            'C:\Users\keoghan',
            $this->getMechanic(['HOME'=>'C:\Users\keoghan'])->getUserHomePath()
        );
    }
}
