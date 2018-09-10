<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Models\Site;
use App\Nginx\SiteConfBuilder;
use Tests\TestCase;

class SiteTest extends TestCase
{
    /** @test */
    public function it_builds_nginx_config()
    {
        $site = factory(Site::class)->create(['name' => 'klever']);

        $site->buildFiles();

        $this->assertFileExists($site->nginx_conf_path);
    }

    /** @test */
    public function it_returns_additional_attributes()
    {
        factory(Setting::class)->create(['name' => 'domain', 'value' => 'test']);

        $site = factory(Site::class)->create(['name' => 'klever']);

        $this->assertEquals('klever.test', $site->url);
        $this->assertEquals('http://', $site->scheme);
        $this->assertRegExp('/klever.conf/', $site->nginx_conf_path);
        $this->assertEquals('nginx.default.domain', $site->nginx_conf_template);

        $site->secure = true;
        $site->save();

        $this->assertEquals('klever.test', $site->url);
        $this->assertEquals('https://', $site->scheme);
        $this->assertRegExp('/klever.conf/', $site->nginx_conf_path);
        $this->assertEquals('nginx.default.domain_secure', $site->nginx_conf_template);
    }

    /** @test */
    public function it_sets_the_nginx_type()
    {
        $this->siteFilesShouldBeBuilt();

        $site = factory(Site::class)->create();

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

        $site = factory(Site::class)->create();
        $site->destroyFiles();
    }

    /** @test */
    public function it_sets_the_php_version()
    {
        $this->siteFilesShouldBeBuilt();

        $site = factory(Site::class)->create();
        $version = factory(PhpVersion::class)->create();

        $site->setPhpVersion($version->id);
        $this->assertEquals($version->fresh(), $site->php_version);
    }

    /** @test */
    public function it_uses_the_default_php_version_if_not_specified()
    {
        $this->siteFilesShouldBeBuilt();

        $site = factory(Site::class)->create();
        $defaultVersion = factory(PhpVersion::class)->create(['default' => true]);

        $site->setPhpVersion();
        $this->assertEquals($defaultVersion->fresh(), $site->php_version);
    }

    /**
     * Ensure that the site config builder is asked to build files.
     *
     * @return void
     */
    protected function siteFilesShouldBeBuilt(): void
    {
        $this->app->extend(SiteConfBuilder::class, function ($builder, $app) {
            $mock = \Mockery::mock(SiteConfBuilder::class);
            $mock->shouldReceive('build')
                ->once();

            return $mock;
        });
    }
}
