<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Console\Cli;
use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Windows;
use Illuminate\Filesystem\Filesystem;
use App\Support\Mutagen\CannotInstallMutagen;
use App\Support\Mutagen\Mutagen;

class MutagenTest extends MechanicTestCase
{
    protected $cli;

    public function setUp(): void
    {
        parent::setUp();

        $this->cli = \Mockery::mock(Cli::class);
    }

    /** @test */
    public function it_installs_mutagen_on_macos()
    {
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, new Filesystem);

        $this->cli->shouldReceive('passthru')
            ->with('brew install havoc-io/mutagen/mutagen')
            ->once();

        $mutagen->install();
    }

    /** @test */
    public function it_will_not_install_on_linux()
    {
        $this->expectException(CannotInstallMutagen::class);
        $mutagen = new Mutagen(app(Linux::class), $this->cli, new Filesystem);

        $mutagen->install();
    }

    /** @test */
    public function it_will_not_install_on_windows()
    {
        $this->expectException(CannotInstallMutagen::class);
        $mutagen = new Mutagen(app(Windows::class), $this->cli, new Filesystem);

        $mutagen->install();
    }

//    /** @test */
//    public function it_will_start_the_daemon_when_porter_starts_and_mutagen_is_active()
//    {
//    }
//
//    /** @test */
//    public function it_will_stop_the_daemon_when_porter_stops_and_mutagen_is_active()
//    {
//    }
//
//    /** @test */
//    public function it_stops_fast_dirs_from_being_mounted_by_docker_when_active()
//    {
//    }
//
//    /** @test */
//    public function it_creates_the_mounts_it_needs_when_active()
//    {
//    }
}
