<?php

namespace Tests\Unit\Support\Xdebug;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Models\Site;
use App\Support\Console\Cli;
use App\Support\XDebug\XDebug;
use App\Support\Mechanics\Linux;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Windows;
use Tests\Unit\Support\Concerns\MocksPorter;
use App\Support\Mutagen\CannotInstallMutagen;
use App\Support\Mutagen\Mutagen;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Tests\BaseTestCase;
use App\Support\Console\DockerCompose\CliCommand;
use Tests\Unit\Support\Concerns\MocksDockerCompose;

class XdebugTest extends BaseTestCase
{
    use MocksPorter;
    use MocksDockerCompose;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockDockerCompose();
    }

    /** @test */
    public function it_turns_xdebug_on()
    {
        $version = factory(PhpVersion::class)
            ->create(['version_number' => '7.4', 'default' => true]);


        $command = \Mockery::mock(CliCommand::class);

        $xdebug = new XDebug($this->porter, $this->dockerCompose);

        $from = 'xdebug.bak';
        $to = 'xdebug.ini';

        $this->dockerCompose->shouldReceive('execContainer')
            ->with('php_fpm_7-4')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('append')
            ->with("mv /etc/php/{$version->version_number}/mods-available/{$from} /etc/php/{$version->version_number}/mods-available/{$to}")
            ->once()
            ->andReturn($command);

        $command->shouldReceive('interactive')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('perform')
            ->once()
            ->andReturn(0);

        $this->porter->shouldReceive('softRestart')
            ->with('php_fpm_7-4')
            ->once();

        $xdebug->turnOn();
    }

    /** @test */
    public function if_xdebug_is_already_on_we_dont_restart_the_containers()
    {
        $version = factory(PhpVersion::class)
            ->create(['version_number' => '7.4', 'default' => true]);


        $command = \Mockery::mock(CliCommand::class);

        $xdebug = new XDebug($this->porter, $this->dockerCompose);

        $from = 'xdebug.bak';
        $to = 'xdebug.ini';

        $this->dockerCompose->shouldReceive('execContainer')
            ->with('php_fpm_7-4')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('append')
            ->with("mv /etc/php/{$version->version_number}/mods-available/{$from} /etc/php/{$version->version_number}/mods-available/{$to}")
            ->once()
            ->andReturn($command);

        $command->shouldReceive('interactive')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('perform')
            ->once()
            ->andReturn(1);

        $this->porter->shouldNotReceive('softRestart')
            ->with('php_fpm_7-4');

        $xdebug->turnOn();
    }

    /** @test */
    public function it_turns_xdebug_off()
    {
        $version = factory(PhpVersion::class)
            ->create(['version_number' => '7.4', 'default' => true]);


        $command = \Mockery::mock(CliCommand::class);

        $xdebug = new XDebug($this->porter, $this->dockerCompose);

        $from = 'xdebug.ini';
        $to = 'xdebug.bak';

        $this->dockerCompose->shouldReceive('execContainer')
            ->with('php_fpm_7-4')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('append')
            ->with("mv /etc/php/{$version->version_number}/mods-available/{$from} /etc/php/{$version->version_number}/mods-available/{$to}")
            ->once()
            ->andReturn($command);

        $command->shouldReceive('interactive')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('perform')
            ->once()
            ->andReturn(0);

        $this->porter->shouldReceive('softRestart')
            ->with('php_fpm_7-4')
            ->once();

        $xdebug->turnOff();
    }

    /** @test */
    public function if_xdebug_is_already_off_we_dont_restart_the_containers()
    {
        $version = factory(PhpVersion::class)
            ->create(['version_number' => '7.4', 'default' => true]);


        $command = \Mockery::mock(CliCommand::class);

        $xdebug = new XDebug($this->porter, $this->dockerCompose);

        $from = 'xdebug.ini';
        $to = 'xdebug.bak';

        $this->dockerCompose->shouldReceive('execContainer')
            ->with('php_fpm_7-4')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('append')
            ->with("mv /etc/php/{$version->version_number}/mods-available/{$from} /etc/php/{$version->version_number}/mods-available/{$to}")
            ->once()
            ->andReturn($command);

        $command->shouldReceive('interactive')
            ->once()
            ->andReturn($command);

        $command->shouldReceive('perform')
            ->once()
            ->andReturn(1);

        $this->porter->shouldNotReceive('softRestart')
            ->with('php_fpm_7-4');

        $xdebug->turnOff();
    }
}
