<?php

namespace Tests\Unit\Commands\Meilisearch;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OnTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_meilisearch_on()
    {
        $this->porter->shouldReceive('turnOnService')->with('meilisearch')->once();

        $this->artisan('meilisearch:on');
    }
}
