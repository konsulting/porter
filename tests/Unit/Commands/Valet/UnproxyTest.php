<?php

namespace Tests\Unit\Commands\Valet;

use App\Models\Site;
use App\Support\Valet\Valet;
use Tests\BaseTestCase;

class UnproxyTest extends BaseTestCase
{
    /** @test */
    public function it_will_unproxy_all_sites()
    {
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        factory(Site::class, 2)->create(['secure' => true]);

        $valet->shouldReceive('removeSite')->times(2);

        $this->artisan('valet:unproxy', ['--all' => true]);
    }

    /** @test */
    public function it_will_unproxy_a_single_site()
    {
        $this->withoutExceptionHandling();
        app()->instance(Valet::class, $valet = \Mockery::mock(Valet::class));

        $site = factory(Site::class)->create(['secure' => true, 'name' => 'dummy']);

        $valet->shouldReceive('removeSite')->once()->with(
            \Mockery::on(function ($arg) use ($site) {
                return $arg->is($site);
            })
        );

        $this->artisan('valet:unproxy', ['site' => 'dummy']);
    }
}
