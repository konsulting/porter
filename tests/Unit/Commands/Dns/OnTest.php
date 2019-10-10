<?php

namespace Tests\Unit\Commands\Dns;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OnTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_the_dns_on()
    {
        $this->porter->shouldReceive('turnOnService')->with('dns')->once();

        $this->artisan('dns:on');
    }
}
