<?php

namespace Tests\Unit\Support;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Models\Site;
use App\Support\Console\Cli;
use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Windows;
use App\Support\Mutagen\CannotInstallMutagen;
use App\Support\Mutagen\Mutagen;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Tests\BaseTestCase;

class MutagenTest extends BaseTestCase
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
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, new Filesystem());

        $this->cli->shouldReceive('passthru')
            ->with('brew install havoc-io/mutagen/mutagen')
            ->once();

        $mutagen->install();
    }

    /** @test */
    public function it_will_not_install_on_linux()
    {
        $this->expectException(CannotInstallMutagen::class);
        $mutagen = new Mutagen(app(Linux::class), $this->cli, new Filesystem());

        $mutagen->install();
    }

    /** @test */
    public function it_will_not_install_on_windows()
    {
        $this->expectException(CannotInstallMutagen::class);
        $mutagen = new Mutagen(app(Windows::class), $this->cli, new Filesystem());

        $mutagen->install();
    }

    /** @test */
    public function it_starts_the_mutagen_daemon_as_appropriate()
    {
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, new Filesystem());

        $this->cli->shouldNotReceive('exec');
        $mutagen->startDaemon();

        Setting::updateOrCreate('use_mutagen', 'on');
        $this->cli->shouldReceive('exec')->with('mutagen daemon start')->once();
        $mutagen->startDaemon();
    }

    /** @test */
    public function it_stops_the_mutagen_daemon_as_appropriate()
    {
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, new Filesystem());

        $this->cli->shouldNotReceive('exec');
        $mutagen->stopDaemon();

        Setting::updateOrCreate('use_mutagen', 'on');
        $this->cli->shouldReceive('exec')->with('mutagen sync terminate -a')->once();
        $this->cli->shouldReceive('exec')->with('mutagen daemon stop')->once();
        $mutagen->stopDaemon();
    }

    /** @test */
    public function it_removes_the_volumes_from_docker_compose_yaml()
    {
        $files = Mockery::mock(Filesystem::class);
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, $files);
        Setting::updateOrCreate('use_mutagen', 'on');
        $v = factory(PhpVersion::class)->create(['version_number' => '7.1']);
        factory(Site::class)->create(['php_version_id' => $v->id]);

        $files->shouldReceive('get')->with('dc.yaml')->andReturn(
            <<<'EOB'
services:
  php_fpm_7-1:
    volumes:
      - volume1
      - volume2
  nginx:
    volumes:
      - volume1
      - volume2
  another:
    volumes:
      - volume1
      - volume2
EOB
        )->once();
        $files->shouldReceive('put')->with(
            'dc.yaml',
            <<<EOB
services:
  php_fpm_7-1:
    volumes:
      - volume2
  nginx:
    volumes:
      - volume2
  another:
    volumes:
      - volume1
      - volume2\n
EOB
        )->once();
        $mutagen->removeVolumesFromDockerCompose('dc.yaml');
    }

    /** @test */
    public function it_creates_sync_processes_for_the_volumes()
    {
        $files = Mockery::mock(Filesystem::class);
        $mutagen = new Mutagen(app(MacOs::class), $this->cli, $files);

        $v = factory(PhpVersion::class)->create(['version_number' => '7.1']);
        factory(Site::class)->create(['php_version_id' => $v->id]);

        $this->cli->shouldNotReceive('passthru');
        $mutagen->syncVolumes();

        Setting::updateOrCreate('use_mutagen', 'on');
        Setting::updateOrCreate('home', '/home');

        $this->cli->shouldReceive('passthru')
            ->with("mutagen sync create --symlink-mode=ignore --ignore-vcs --ignore=['node_modules'] /home \docker://porter_php_fpm_7-1_1/srv/app");

        $this->cli->shouldReceive('passthru')
            ->with("mutagen sync create --symlink-mode=ignore --ignore-vcs --ignore=['node_modules'] /home \docker://porter_nginx_1/srv/app");
    }
}
