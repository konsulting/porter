<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Models\Site;
use App\Porter;
use App\Support\Contracts\Cli;
use App\Support\Nginx\SiteConfBuilder;
use App\Support\Ssl\CertificateBuilder;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Tests\BaseTestCase;

class SiteTest extends BaseTestCase
{
    use ArraySubsetAsserts;

    /** @test */
    public function it_builds_nginx_config()
    {
        $site = Site::factory()->create(['name' => 'klever']);

        $site->buildFiles();

        $this->assertFileExists($site->nginx_conf_path);
    }

    /** @test */
    public function it_returns_additional_attributes()
    {
        Setting::factory()->create(['name' => 'domain', 'value' => 'test']);

        $site = Site::factory()->create(['name' => 'klever']);

        $this->assertEquals('klever.test', $site->url);
        $this->assertEquals('http://', $site->scheme);
        $this->assertMatchesRegularExpression('/klever.conf/', $site->nginx_conf_path);
        $this->assertEquals('nginx.default.domain', $site->nginx_conf_template);

        $site->secure = true;
        $site->save();

        $this->assertEquals('klever.test', $site->url);
        $this->assertEquals('https://', $site->scheme);
        $this->assertMatchesRegularExpression('/klever.conf/', $site->nginx_conf_path);
        $this->assertEquals('nginx.default.domain_secure', $site->nginx_conf_template);
    }

    /** @test */
    public function it_sets_the_nginx_type()
    {
        $this->shouldBuildNginxFiles();
        $this->shouldRestartServing();

        $site = Site::factory()->create();

        $site->setNginxType('my type');
        $this->assertSame('my type', $site->nginx_conf);
    }

    /** @test */
    public function it_destroys_the_site_config_files()
    {
        $this->app->extend(SiteConfBuilder::class, function ($builder, $app) {
            $mock = \Mockery::mock(SiteConfBuilder::class);
            $mock->shouldReceive('destroy')
                ->once();

            return $mock;
        });

        $site = Site::factory()->create();
        $site->destroyFiles();
    }

    /** @test */
    public function it_sets_the_php_version()
    {
        $this->shouldBuildNginxFiles();
        $this->shouldRestartServing();

        $site = Site::factory()->create();
        $version = PhpVersion::factory()->create();

        $site->setPhpVersion($version->id);
        $this->assertEquals($version->fresh(), $site->php_version);
    }

    /** @test */
    public function it_uses_the_default_php_version_if_not_specified()
    {
        $this->shouldBuildNginxFiles();
        $this->shouldRestartServing();

        $site = Site::factory()->create();
        $defaultVersion = PhpVersion::factory()->create(['default' => true]);

        $site->setPhpVersion();
        $this->assertEquals($defaultVersion->fresh(), $site->php_version);
    }

    /** @test */
    public function it_will_resolve_a_site_name_from_a_path()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $this->assertEquals('sample', Site::nameFromPath('/Users/keoghan/Code/sample'));
    }

    /** @test */
    public function it_will_return_null_if_the_path_is_outside_the_home_dir()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $this->assertNull(Site::nameFromPath('/tmp/sample'));
    }

    /** @test */
    public function it_will_resolve_a_simple_site_name()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $this->assertEquals('sample', Site::nameFromPath('sample'));
    }

    /** @test */
    public function it_will_resolve_a_site_name_from_the_current_working_directory()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        app()->instance(Cli::class, \Mockery::mock(Cli::class));

        app(Cli::class)
            ->shouldReceive('currentWorkingDirectory')
            ->andReturn('/Users/keoghan/Code/sample/deep');

        $this->assertEquals('sample', Site::nameFromPath('/Users/keoghan/Code/sample'));
    }

    /** @test */
    public function it_will_resolve_a_site_from_a_simple_name()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->assertEquals(
            $site->getKey(),
            Site::resolveFromPathOrCurrentWorkingDirectory('sample')->getKey()
        );
    }

    /** @test */
    public function it_will_resolve_a_site_from_a_path()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->assertEquals(
            $site->getKey(),
            Site::resolveFromPathOrCurrentWorkingDirectory('/Users/keoghan/Code/sample')->getKey()
        );
    }

    /** @test */
    public function it_will_not_resolve_a_missing_site_from_a_path()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $this->assertNull(
            Site::resolveFromPathOrCurrentWorkingDirectory('/Users/keoghan/Code/sample')
        );
    }

    /** @test */
    public function it_will_resolve_a_site_from_the_current_working_directory()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $site = Site::factory()->create(['name' => 'sample']);

        app()->instance(Cli::class, \Mockery::mock(Cli::class));

        app(Cli::class)
            ->shouldReceive('currentWorkingDirectory')
            ->andReturn('/Users/keoghan/Code/sample');

        $this->assertEquals(
            $site->getKey(),
            Site::resolveFromPathOrCurrentWorkingDirectory()->getKey()
        );
    }

    /** @test */
    public function resolving_sites_or_failing_works()
    {
        Setting::updateOrCreate('home', '/Users/keoghan/Code');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->assertEquals(
            $site->getKey(),
            Site::resolveFromPathOrCurrentWorkingDirectoryOrFail('/Users/keoghan/Code/sample')->getKey()
        );

        try {
            Site::resolveFromPathOrCurrentWorkingDirectoryOrFail('/Users/keoghan/Code/non-existent');
            $this->fail('Should have failed for a missing site.');
        } catch (\Exception $e) {
            $this->assertEquals('Site not found.', $e->getMessage());
        }
    }

    /** @test */
    public function it_will_create_a_site_from_only_a_name()
    {
        $version = PhpVersion::factory()->default()->create();

        $site = Site::createForName('sample');

        self::assertArraySubset([
            'name'           => 'sample',
            'nginx_conf'     => 'default',
            'php_version_id' => $version->getKey(),
            'secure'         => false,
        ], $site->toArray());
    }

    /** @test */
    public function it_will_create_a_site_from_a_name_if_it_doesnt_exist()
    {
        $version = PhpVersion::factory()->default()->create();

        $site = Site::firstOrCreateForName('sample');

        self::assertArraySubset([
            'name'           => 'sample',
            'nginx_conf'     => 'default',
            'php_version_id' => $version->getKey(),
            'secure'         => false,
        ], $site->toArray());

        $found = Site::firstOrCreateForName('sample');

        $this->assertSame($site->getKey(), $found->getKey());
    }

    /** @test */
    public function it_builds_a_certificate()
    {
        Setting::updateOrCreate('domain', 'test');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->shouldAddCertificate('sample.test');

        $site->buildCertificate();
    }

    /** @test */
    public function it_removes_a_certificate()
    {
        Setting::updateOrCreate('domain', 'test');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->shouldRemoveCertificate('sample.test');

        $site->destroyCertificate();
    }

    /** @test */
    public function it_secures_a_site()
    {
        Setting::updateOrCreate('domain', 'test');

        $site = Site::factory()->create(['name' => 'sample']);

        $this->shouldAddCertificate('sample.test');
        $this->shouldBuildNginxFiles();
        $this->shouldRestartServing();

        $site->secure();

        $this->assertEquals(1, $site->refresh()->secure);
    }

    /** @test */
    public function it_unsecures_a_site()
    {
        Setting::updateOrCreate('domain', 'test');

        $site = Site::factory()->create(['name' => 'sample', 'secure' => true]);

        $this->shouldRemoveCertificate('sample.test');
        $this->shouldBuildNginxFiles();
        $this->shouldRestartServing();

        $site->unsecure();

        $this->assertEquals(0, $site->fresh()->secure);
    }

    /** @test */
    public function it_removes_a_site()
    {
        Setting::updateOrCreate('domain', 'test');

        $site = Site::factory()->create(['name' => 'sample', 'secure' => true]);

        $this->shouldRemoveCertificate('sample.test');
        $this->shouldRemoveNginxFiles();
        $this->shouldRestartServing();

        $site->remove();

        $this->assertNull(Site::find($site->getKey()));
    }

    /**
     * Ensure that the site config builder is asked to build files.
     *
     * @return void
     */
    protected function shouldBuildNginxFiles(): void
    {
        $this->app->extend(SiteConfBuilder::class, function ($builder, $app) {
            $mock = \Mockery::mock(SiteConfBuilder::class);
            $mock->shouldReceive('build')
                ->once();

            return $mock;
        });
    }

    /**
     * Ensure that the site config builder is asked to build files.
     *
     * @return void
     */
    protected function shouldRemoveNginxFiles(): void
    {
        $this->app->extend(SiteConfBuilder::class, function ($builder, $app) {
            $mock = \Mockery::mock(SiteConfBuilder::class);
            $mock->shouldReceive('destroy')
                ->once();

            return $mock;
        });
    }

    /**
     * Ensure Porter is asked to restart serving sites.
     *
     * @return void
     */
    protected function shouldRestartServing()
    {
        $this->app->extend(Porter::class, function ($builder, $app) {
            $mock = \Mockery::mock(Porter::class);
            $mock->shouldReceive('restartServing')
                ->once();

            return $mock;
        });
    }

    /**
     * Ensure that the certificate builder is asked to build a certificate.
     *
     * @param $forUrl
     *
     * @return void
     */
    protected function shouldAddCertificate($forUrl): void
    {
        $this->app->extend(CertificateBuilder::class, function ($builder, $app) use ($forUrl) {
            $mock = \Mockery::mock(CertificateBuilder::class);
            $mock->shouldReceive('build')
                ->with($forUrl)
                ->once();

            return $mock;
        });
    }

    /**
     * Ensure that the certificate builder is asked to remove a certificate.
     *
     * @param $forUrl
     *
     * @return void
     */
    protected function shouldRemoveCertificate($forUrl): void
    {
        $this->app->extend(CertificateBuilder::class, function ($builder, $app) use ($forUrl) {
            $mock = \Mockery::mock(CertificateBuilder::class);
            $mock->shouldReceive('destroy')
                ->with($forUrl)
                ->once();

            return $mock;
        });
    }
}
