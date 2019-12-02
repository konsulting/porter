<?php

namespace Tests\Unit\Commands\DockerSync;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_will_turn_mutagen_off()
    {
        $this->porter->shouldReceive('turnOffService')->with('docker-sync')->once();

        $this->artisan('docker-sync:off');
    }
}
