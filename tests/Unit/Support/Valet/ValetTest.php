<?php

namespace Tests\Unit\Support\Valet;

use App\Models\Setting;
use App\Models\Site;
use App\Support\Console\Cli;
use App\Support\Console\ConsoleWriter;
use App\Support\Valet\Valet;
use Illuminate\Support\Facades\Artisan;
use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class ValetTest extends BaseTestCase
{
    use MocksPorter;

    protected $cli;
    protected $writer;

    public function setUp(): void
    {
        parent::setUp();

        $this->cli = \Mockery::mock(Cli::class);
        $this->writer = \Mockery::mock(ConsoleWriter::class);
    }

    /** @test */
    public function it_will_turn_on_valet_compat()
    {
        factory(Site::class)->create(['name' => 'dummy']);

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        Artisan::shouldReceive('call')->with('dns:off')->once();

        $this->cli->shouldReceive('execRealTime')
            ->with('valet start')
            ->once();

        $this->porter->shouldReceive('compose')->once();
        $this->porter->shouldReceive('restart')->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxy dummy http://127.0.0.1:8008')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Added dummy proxy for Valet')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Completed setting up valet compatibility')
            ->once();

        $valet->turnOn();

        $this->assertEquals('on', setting('use_valet'));
        $this->assertEquals(8008, setting('http_port'));
        $this->assertEquals(8443, setting('https_port'));
    }

    /** @test */
    public function it_will_not_turn_on_valet_if_already_on()
    {
        Setting::updateOrCreate('use_valet', 'on');

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Valet compatibility already complete')
            ->once();

        $valet->turnOn();
    }

    /** @test */
    public function it_will_not_turn_off_valet_if_already_off()
    {
        Setting::updateOrCreate('use_valet', 'off');

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Valet compatibility already off')
            ->once();

        $valet->turnOff();
    }

    /** @test */
    public function it_will_turn_off_valet_compat()
    {
        Setting::updateOrCreate('use_valet', 'on');

        factory(Site::class)->create(['name' => 'dummy']);

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet unproxy dummy')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Removed Valet proxy for dummy')
            ->once();

        $this->cli->shouldReceive('execRealTime')
            ->with('valet stop')
            ->once();

        $this->cli->shouldReceive('execRealTime')
            ->with('sudo brew services stop dnsmasq')
            ->once();

        Artisan::shouldReceive('call')->with('dns:on')->once();

        $this->porter->shouldReceive('compose')->once();
        $this->porter->shouldReceive('restart')->once();

        $this->writer->shouldReceive('line')
            ->with('Completed removing valet compatibility')
            ->once();

        $valet->turnOff();

        $this->assertEquals('off', setting('use_valet'));
        $this->assertEquals(80, setting('http_port'));
        $this->assertEquals(443, setting('https_port'));
    }

    /** @test */
    public function it_will_add_a_secure_site()
    {
        $site = factory(Site::class)->create(['name' => 'dummy', 'secure' => 1]);

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxy dummy https://127.0.0.1:8443')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Added dummy proxy for Valet')
            ->once();

        $valet->addSite($site);
    }

    /** @test */
    public function it_will_remove_an_existing_proxy_when_adding_a_site()
    {
        $site = factory(Site::class)->create(['name' => 'dummy', 'secure' => 1]);

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once()
            ->andReturn('dummy');

        $this->cli->shouldReceive('exec')
            ->with('valet unproxy dummy')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Removed Valet proxy for dummy')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxy dummy https://127.0.0.1:8443')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Added dummy proxy for Valet')
            ->once();

        $valet->addSite($site);
    }

    /** @test */
    public function it_will_remove_a_proxy()
    {
        $site = factory(Site::class)->create(['name' => 'dummy', 'secure' => 1]);

        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet unproxy dummy')
            ->once();

        $this->writer->shouldReceive('line')
            ->with('Removed Valet proxy for dummy')
            ->once();

        $valet->removeSite($site);
    }

    /** @test */
    public function it_will_list_sites_that_are_proxied_with_valet()
    {
        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once()
            ->andReturn('dummy');

        $this->assertEquals('dummy', $valet->listSites());
    }

    /** @test */
    public function it_can_check_if_a_site_is_proxied()
    {
        $site = factory(Site::class)->create(['name' => 'dummy', 'secure' => 1]);
        $valet = new Valet($this->porter, $this->cli, $this->writer);

        $this->writer->shouldReceive('info')
            ->with('Requires Sudo permissions for Valet')
            ->once();

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once();

        $this->assertEquals(false, $valet->isProxied($site));

        $this->cli->shouldReceive('exec')
            ->with('valet proxies')
            ->once()
            ->andReturn('dummy');

        $this->assertEquals(true, $valet->isProxied($site));
    }
}
