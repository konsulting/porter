<?php

namespace Tests\Unit\Commands\Mutagen;

use App\Support\Mutagen\Mutagen;
use Tests\BaseTestCase;

class InstallTest extends BaseTestCase
{
    /** @test */
    public function it_will_run_the_mutagen_installer()
    {
        $mutagen = \Mockery::mock(Mutagen::class);
        $this->app->instance(Mutagen::class, $mutagen);
        $mutagen->shouldReceive('install')->withNoArgs()->once();

        $this->artisan('mutagen:install');
    }
}
