<?php

namespace Tests\Unit\Commands\DockerSync;

use Tests\BaseTestCase;
use App\Support\DockerSync\DockerSync;

class InstallTest extends BaseTestCase
{
    /** @test */
    public function it_will_run_the_docker_sync_installer()
    {
        $dockerSync = \Mockery::mock(DockerSync::class);
        $this->app->instance(DockerSync::class, $dockerSync);
        $dockerSync->shouldReceive('install')->withNoArgs()->once();

        $this->artisan('docker-sync:install');
    }
}
