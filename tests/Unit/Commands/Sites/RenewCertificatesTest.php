<?php

namespace Tests\Unit\Commands\Sites;

use App\Models\Site;
use App\Support\Ssl\CertificateBuilder;
use Mockery\MockInterface;
use Tests\BaseTestCase;

class RenewCertificatesTest extends BaseTestCase
{
    /** @test */
    public function it_renews_the_certificates()
    {
        Site::factory()->create(['secure' => true]);

        $builder = $this->mockCertificateBuilder();
        $builder->shouldReceive('clearCertificates')->with(false)->once();
        $builder->shouldReceive('build')->with('porter_default')->once();

        foreach (Site::all() as $site) {
            $builder->shouldReceive('build')->with($site->url)->once();
        }

        $this->app->instance(CertificateBuilder::class, $builder);

        $this->artisan('site:renew-certs');
    }

    /** @test */
    public function it_will_clear_ca_certs()
    {
        $builder = $this->mockCertificateBuilder();
        $builder->shouldReceive('clearCertificates')->with(true)->once();
        $builder->shouldReceive('build')->zeroOrMoreTimes();

        $this->app->instance(CertificateBuilder::class, $builder);

        $this->artisan('site:renew-certs', ['--clear-ca' => true]);
    }

    /**
     * Mock the CertificateBuilder.
     *
     * @return MockInterface
     */
    protected function mockCertificateBuilder()
    {
        $builder = \Mockery::mock(CertificateBuilder::class);

        $this->app->instance(CertificateBuilder::class, $builder);

        return $builder;
    }
}
