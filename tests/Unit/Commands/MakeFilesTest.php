<?php

namespace Tests\Unit\Commands;

use App\Commands\DockerCompose\Start;
use App\Commands\DockerCompose\Stop;
use App\Models\Site;
use App\Porter;
use App\Support\Nginx\SiteConfBuilder;
use App\Support\Ssl\CertificateBuilder;
use Mockery\MockInterface;
use Tests\BaseTestCase;
use Tests\Unit\Support\Concerns\MocksPorter;

class MakeFilesTest extends BaseTestCase
{
    use MocksPorter;

    /** @var Porter|MockInterface */
    protected $porter;

    /** @test */
    public function it_will_remake_the_files()
    {
        $conf = $this->mockSiteConfigBuilder();
        $cert = $this->mockCertificateBuilder();

        factory(Site::class, 2)->create([]);

        $this->porter->shouldReceive('isUp')->once()->andReturn(false);
        $this->porter->shouldReceive('compose')->once();

        $conf->shouldReceive('build')->twice();
        $cert->shouldReceive('build')->with('porter_default')->once();

        $this->artisan('make-files');
    }

    /** @test */
    public function it_will_remake_the_files_and_restart_porter()
    {
        $this->mockPorterCommand(Start::class);
        $this->mockPorterCommand(Stop::class);

        $conf = $this->mockSiteConfigBuilder();
        $cert = $this->mockCertificateBuilder();

        factory(Site::class, 2)->create([]);

        $this->porter->shouldReceive('isUp')->once()->andReturn(true);
        $this->porter->shouldReceive('compose')->once();

        $conf->shouldReceive('build')->twice();
        $cert->shouldReceive('build')->with('porter_default')->once();

        $this->artisan('make-files');
    }

    /**
     * Mock the SiteConfigBuilder.
     *
     * @return MockInterface
     */
    protected function mockSiteConfigBuilder()
    {
        $conf = \Mockery::mock(SiteConfBuilder::class);

        $this->app->instance(SiteConfBuilder::class, $conf);

        return $conf;
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
