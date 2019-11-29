<?php

namespace Tests\Unit\Commands\DockerSync;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OnTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_will_turn_mutagen_on()
    {
        $this->porter->shouldReceive('turnOnService')->with('docker-sync')->once();

        $this->artisan('docker-sync:on');
    }
}
