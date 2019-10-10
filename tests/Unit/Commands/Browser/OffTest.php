<?php

namespace Tests\Unit\Commands\Browser;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_the_browser_off()
    {
        $this->porter->shouldReceive('turnOffService')->with('browser')->once();

        $this->artisan('browser:off');
    }
}
