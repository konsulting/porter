<?php

namespace Tests\Unit\Commands\Elasticsearch;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OffTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_elasticsearch_off()
    {
        $this->porter->shouldReceive('turnOffService')->with('elasticsearch')->once();

        $this->artisan('elasticsearch:off');
    }
}
