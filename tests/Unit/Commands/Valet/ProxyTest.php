<?php

namespace Tests\Unit\Commands\Valet;

use App\Models\Site;
use App\Support\Valet\Valet;
use Tests\BaseTestCase;

class ProxyTest extends BaseTestCase
{
    /** @test */
    public function it_will_do_all_site_proxies()
    {
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        factory(Site::class, 2)->create(['secure' => true]);

        $valet->shouldReceive('addSite')->times(2);

        $this->artisan('valet:proxy', ['--all' => true]);
    }

    /** @test */
    public function it_will_proxy_a_single_site()
    {
        $this->withoutExceptionHandling();
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        $site = factory(Site::class)->create(['secure' => true, 'name' => 'dummy']);

        $valet->shouldReceive('addSite')->once()->with(
            \Mockery::on(function ($arg) use ($site) {
                return $arg->is($site);
            })
        );

        $this->artisan('valet:proxy', ['site' => 'dummy']);
    }
}
