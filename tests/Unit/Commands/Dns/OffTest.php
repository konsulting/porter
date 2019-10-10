<?php

namespace Tests\Unit\Commands\Dns;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_the_dns_off()
    {
        $this->porter->shouldReceive('turnOffService')->with('dns')->once();

        $this->artisan('dns:off');
    }
}
