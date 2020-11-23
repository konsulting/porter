<?php

namespace Tests\Unit\Commands\Valet;

use App\Support\Valet\Valet;
use Tests\BaseTestCase;

class OnTest extends BaseTestCase
{
    /** @test */
    public function it_turns_valet_on()
    {
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        $valet->shouldReceive('turnOn')->once();

        $this->artisan('valet:on');
    }
}
