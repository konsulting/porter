<?php

namespace Tests\Unit\Commands\DockerSync;

use App\Support\Mutagen\Mutagen;
use Tests\BaseTestCase;

class InstallTest extends BaseTestCase
{
    /** @test */
    public function it_will_run_the_docker_sync_installer()
    {
        $mutagen = \Mockery::mock(Mutagen::class);
        $this->app->instance(Mutagen::class, $mutagen);
        $mutagen->shouldReceive('install')->withNoArgs()->once();

        $this->artisan('docker-sync:install');
    }
}
