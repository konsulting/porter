<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Mechanics\ChooseMechanic;
use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Untrained;
use App\Support\Mechanics\Windows;
use Tests\TestCase;

class ChooseMechanicTest extends TestCase
{
    /** @test */
    public function it_chooses_correctly_for_the_os()
    {
        $this->assertInstanceOf(MacOs::class, ChooseMechanic::forOs('DAR'), 'MacOS mechanic not chosen.');
        $this->assertInstanceOf(Windows::class, ChooseMechanic::forOs('WIN'), 'Windows mechanic not chosen.');
        $this->assertInstanceOf(Linux::class, ChooseMechanic::forOs('LINUX'), 'Linux mechanic not chosen.');
        $this->assertInstanceOf(Untrained::class, ChooseMechanic::forOs('OTHER'), 'Untrained mechanic not chosen.');
    }
}
