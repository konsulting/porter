<?php


namespace Tests\Unit\Support\Php;


use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Php\Xdebug;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class XdebugTest extends BaseTestCase
{
    /** @test */
    public function it_turns_xdebug_off_for_all_php_services()
    {
        factory(PhpVersion::class)->create(['version_number' => '5.6']);
        factory(PhpVersion::class)->create(['version_number' => '7.0']);

        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('configPath')->once()->andReturn('');

        $filesystem = Mockery::mock(Filesystem::class);

        $this->expectsOffToOn($filesystem, 'php_cli_5-6');
        $this->expectsOffToOn($filesystem, 'php_fpm_5-6');
        $this->expectsOffToOn($filesystem, 'php_cli_7-0');
        $this->expectsOffToOn($filesystem, 'php_fpm_7-0');

        $xdebug = new Xdebug($porterLibrary, $filesystem);
        $xdebug->turnOff();
    }

    /** @test */
    public function it_turns_xdebug_off_for_one_php_services()
    {
        factory(PhpVersion::class)->create(['version_number' => '5.6']);
        factory(PhpVersion::class)->create(['version_number' => '7.0']);

        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('configPath')->once()->andReturn('');

        $filesystem = Mockery::mock(Filesystem::class);

        $this->expectsOffToOn($filesystem, 'php_cli_5-6');
        $this->expectsOffToOn($filesystem, 'php_fpm_5-6');

        $xdebug = new Xdebug($porterLibrary, $filesystem);
        $xdebug->turnOff(PhpVersion::findByDirtyVersionNumber('5.6'));
    }

    /** @test */
    public function it_turns_xdebug_on_for_all_php_services()
    {
        factory(PhpVersion::class)->create(['version_number' => '5.6']);
        factory(PhpVersion::class)->create(['version_number' => '7.0']);

        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('configPath')->once()->andReturn('');

        $filesystem = Mockery::mock(Filesystem::class);

        $this->expectsOnToOff($filesystem, 'php_cli_5-6');
        $this->expectsOnToOff($filesystem, 'php_fpm_5-6');
        $this->expectsOnToOff($filesystem, 'php_cli_7-0');
        $this->expectsOnToOff($filesystem, 'php_fpm_7-0');

        $xdebug = new Xdebug($porterLibrary, $filesystem);
        $xdebug->turnon();
    }

    /** @test */
    public function it_turns_xdebug_on_for_one_php_services()
    {
        factory(PhpVersion::class)->create(['version_number' => '5.6']);
        factory(PhpVersion::class)->create(['version_number' => '7.0']);

        $porterLibrary = Mockery::mock(PorterLibrary::class);
        $porterLibrary->shouldReceive('configPath')->once()->andReturn('');

        $filesystem = Mockery::mock(Filesystem::class);

        $this->expectsOnToOff($filesystem, 'php_cli_5-6');
        $this->expectsOnToOff($filesystem, 'php_fpm_5-6');

        $xdebug = new Xdebug($porterLibrary, $filesystem);
        $xdebug->turnon(PhpVersion::findByDirtyVersionNumber('5.6'));
    }

    protected function expectsOffToOn(MockInterface $filesystem, $version)
    {
        $file = $version . '/xdebug.ini';

        $filesystem->shouldReceive('get')->once()->with('/'.$file)
            ->andReturn("xdebug.remote_enable=1\r\nxdebug.default_enable=1");
        $filesystem->shouldReceive('put')->once()
            ->with('/'.$file, "xdebug.remote_enable=0\r\nxdebug.default_enable=0");
    }

    protected function expectsOnToOff(MockInterface $filesystem, $version)
    {
        $file = $version . '/xdebug.ini';

        $filesystem->shouldReceive('get')->once()->with('/'.$file)
            ->andReturn("xdebug.remote_enable=0\r\nxdebug.default_enable=0");
        $filesystem->shouldReceive('put')->once()
            ->with('/'.$file, "xdebug.remote_enable=1\r\nxdebug.default_enable=1");
    }
}
