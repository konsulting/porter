<?php

namespace Tests\Unit\Support\DnsMasq;

use App\PorterLibrary;
use App\Support\Dnsmasq\Config;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Tests\BaseTestCase;

class ConfigTest extends BaseTestCase
{
    /** @test */
    public function it_updates_the_ip_address()
    {
        $files = Mockery::mock(Filesystem::class);
        $porterLibrary = Mockery::mock(PorterLibrary::class);

        $config = new Config($files, $porterLibrary);

        $files->shouldReceive('get')->once()->andReturn('/0.0.0.0');
        $files->shouldReceive('makeDirectory')->once();
        $files->shouldReceive('put')->with('/dnsmasq/dnsmasq.conf', '/1.1.1.1')->once();

        $porterLibrary->shouldReceive('configPath')->times(3)->andReturn('');

        $config->updateIp('1.1.1.1');
    }

    /** @test */
    public function it_updates_the_domain()
    {
        $files = Mockery::mock(Filesystem::class);
        $porterLibrary = Mockery::mock(PorterLibrary::class);

        $config = new Config($files, $porterLibrary);

        $files->shouldReceive('get')->once()->andReturn('/.dev/0.0.0.0');
        $files->shouldReceive('makeDirectory')->once();
        $files->shouldReceive('put')->with('/dnsmasq/dnsmasq.conf', '/.test/0.0.0.0')->once();

        $porterLibrary->shouldReceive('configPath')->times(3)->andReturn('');

        $config->updateDomain('dev', 'test');
    }
}
