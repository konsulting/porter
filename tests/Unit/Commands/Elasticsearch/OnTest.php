<?php

namespace Tests\Unit\Commands\Elasticsearch;

use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class OnTest extends BaseTestCase
{
    use MocksPorter;

    /** @test */
    public function it_turns_elasticsearch_on()
    {
        $this->porter->shouldReceive('turnOnService')->with('elasticsearch')->once();

        $this->artisan('elasticsearch:on');
    }
}
