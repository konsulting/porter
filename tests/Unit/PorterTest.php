<?php

namespace Tests\Unit;

use App\Porter;
use Tests\TestCase;

class PorterTest extends TestCase
{
    /** @test */
    public function it_builds_the_docker_compose_yaml()
    {
        app(Porter::class)->compose();

        $this->assertFileExists(config('app.docker-compose-file'));
    }
}
