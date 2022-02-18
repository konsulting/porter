<?php

namespace Tests\Unit\Commands\Meilisearch;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_meilisearch_off()
    {
        $this->porter->shouldReceive('turnOffService')->with('meilisearch')->once();

        $this->artisan('meilisearch:off');
    }
}
