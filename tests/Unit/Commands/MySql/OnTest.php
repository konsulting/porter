<?php

namespace Tests\Unit\Commands\MySql;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OnTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_the_browser_on()
    {
        $this->porter->shouldReceive('turnOnService')->with('mysql')->once();

        $this->artisan('mysql:on');
    }
}
