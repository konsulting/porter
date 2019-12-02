<?php

namespace Tests\Unit\Support;

use Mockery;
use App\Models\Site;
use App\PorterLibrary;
use Tests\BaseTestCase;
use App\Models\Setting;
use App\Models\PhpVersion;
use App\Support\Console\Cli;
use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Windows;
use App\Support\DockerSync\DockerSync;
use App\Support\DockerSync\CannotInstallDockerSync;
use Illuminate\Filesystem\Filesystem;

class DockerSyncTest extends BaseTestCase
{
    protected $cli;

    public function setUp(): void
    {
        parent::setUp();

        $this->cli = \Mockery::mock(Cli::class);
    }

    /** @test */
    public function it_installs_docker_sync_on_macos()
    {
        $dockerSync = new DockerSync(app(MacOs::class), $this->cli, new Filesystem(), app(PorterLibrary::class));

        $this->cli->shouldReceive('passthru')
            ->with('gem install --user-install docker-sync')
            ->once();

        $dockerSync->install();
    }

    /** @test */
    public function it_will_not_install_on_linux()
    {
        $this->expectException(CannotInstallDockerSync::class);
        $dockerSync = new DockerSync(app(Linux::class), $this->cli, new Filesystem(), app(PorterLibrary::class));

        $dockerSync->install();
    }

    /** @test */
    public function it_will_not_install_on_windows()
    {
        $this->expectException(CannotInstallDockerSync::class);
        $dockerSync = new DockerSync(app(Windows::class), $this->cli, new Filesystem(), app(PorterLibrary::class));

        $dockerSync->install();
    }

    /** @test */
    public function it_starts_the_docker_sync_daemon_as_appropriate()
    {
        $dockerSync = new DockerSync(app(MacOs::class), $this->cli, new Filesystem(), $pl = app(PorterLibrary::class));

        $this->cli->shouldNotReceive('execRealTime');
        $dockerSync->startDaemon();
        Setting::updateOrCreate('use_docker-sync', 'on');
        $this->cli->shouldReceive('exec')
            ->with('ruby -r rubygems -e \'puts Gem.user_dir\'')
            ->once();
        $this->cli->shouldReceive('execRealTime')
            ->with('/bin/docker-sync start --config="'.$pl->path().'/docker-sync.yml"')
            ->once();
        $dockerSync->startDaemon();
    }

    /** @test */
    public function it_stops_the_docker_sync_daemon_as_appropriate()
    {
        $dockerSync = new DockerSync(app(MacOs::class), $this->cli, new Filesystem(), $pl = app(PorterLibrary::class));

        $this->cli->shouldNotReceive('exec');
        $dockerSync->stopDaemon();

        Setting::updateOrCreate('use_docker-sync', 'on');
        $this->cli->shouldReceive('exec')
            ->with('ruby -r rubygems -e \'puts Gem.user_dir\'')
            ->once();
        $this->cli->shouldReceive('execRealTime')
            ->with('/bin/docker-sync stop --config="'.$pl->path().'/docker-sync.yml"')
            ->once();
        $dockerSync->stopDaemon();
    }

    /** @test */
    public function it_adjusts_the_docker_compose_yaml()
    {
        $files = Mockery::mock(Filesystem::class);
        $dockerSync = new DockerSync(app(MacOs::class), $this->cli, $files, app(PorterLibrary::class));
        Setting::updateOrCreate('use_docker-sync', 'on');
        Setting::updateOrCreate('home', 'volume1');

        $v = factory(PhpVersion::class)->create(['version_number' => '7.1']);
        factory(Site::class)->create(['php_version_id' => $v->id]);

        $files->shouldReceive('get')->with('dcdir/dc.yaml')->andReturn(<<<EOB
services:
  php_cli_7-1:
    volumes:
      - volume1:/srv/app
      - volume2
  php_fpm_7-1:
    volumes:
      - volume1:/srv/app
      - volume2
  node:
    volumes:
      - volume1:/srv/app
      - volume2
  nginx:
    volumes:
      - volume1:/srv/app
      - volume2
  another:
    volumes:
      - volume1:/srv/app
      - volume2
EOB
)->once();
        $files->shouldReceive('put')->with('dcdir/dc.yaml', <<<EOB
services:
  php_cli_7-1:
    volumes:
      - 'home:/srv/app:nocopy'
      - volume2
  php_fpm_7-1:
    volumes:
      - 'home:/srv/app:nocopy'
      - volume2
  node:
    volumes:
      - 'home:/srv/app:nocopy'
      - volume2
  nginx:
    volumes:
      - 'home:/srv/app:nocopy'
      - volume2
  another:
    volumes:
      - 'volume1:/srv/app'
      - volume2
volumes:
  home:
    external: true\n
EOB
)->once();

        $files->shouldReceive('put')->with('dcdir/docker-sync.yml', <<<EOB
version: 2
syncs:
  home:
    src: volume1
    watch_excludes:
      - '.*/.git'
      - '.*/node_modules'\n
EOB
        )->once();
        $dockerSync->adjustDockerComposeSetup('dcdir/dc.yaml');
    }
}
