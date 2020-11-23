<?php

namespace Tests\Unit\Commands\Valet;

use App\Support\Valet\Valet;
use Tests\BaseTestCase;

class OffTest extends BaseTestCase
{
    /** @test */
    public function it_turns_valet_off()
    {
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        $valet->shouldReceive('turnOff')->once();

        $this->artisan('valet:off');
    }
}
