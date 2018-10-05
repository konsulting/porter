<?php

namespace Tests\Unit\Commands;

use App\Commands\MakeFiles;
use App\Commands\Site\RenewCertificates;
use App\Models\Setting;
use App\Support\Dnsmasq\Config;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Tests\BaseTestCase;

class DomainTest extends BaseTestCase
{
    /** @test */
    public function it_outputs_the_current_domain()
    {
        Setting::updateOrCreate('domain', 'test');

        $this->artisan('domain');

        $this->assertRegExp('/\'test\'/', Artisan::output());
    }

    /** @test */
    public function it_updates_the_domain()
    {
        $this->mockPorterCommand(RenewCertificates::class);
        $this->mockPorterCommand(MakeFiles::class);

        app()->instance(Config::class, $config = \Mockery::mock(Config::class));

        Setting::updateOrCreate('domain', 'old');

        $config->shouldReceive('updateDomain')->with('old', 'test');

        $this->artisan('domain', ['domain' => 'test']);

        $this->assertEquals('test', setting('domain'));
    }
}
