<?php

namespace Tests\Unit;

use App\Setting;
use App\Site;
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
        factory(Setting::class)->create(['name' => 'tld', 'value' => 'test']);

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
}
