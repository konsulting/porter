<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Models\Site;
use App\Porter;
use App\Support\Contracts\Cli;
use App\Support\Nginx\SiteConfBuilder;
use App\Support\Ssl\CertificateBuilder;
use Tests\BaseTestCase;

class HelpersTest extends BaseTestCase
{
    /** @test */
    public function setting_returns_all_settings()
    {
        Setting::updateOrCreate('test1', 'testa');
        Setting::updateOrCreate('test2', 'testb');

        $this->assertSame([
            'test1' => 'testa',
            'test2' => 'testb',
        ], setting()->toArray());
    }

    /** @test */
    public function setting_returns_a_setting()
    {
        Setting::updateOrCreate('test1', 'testa');
        Setting::updateOrCreate('test2', 'testb');

        $this->assertSame('testa', setting('test1'));
    }

    /** @test */
    public function setting_returns_null_by_default_when_a_setting_doesnt_exist()
    {
        Setting::updateOrCreate('test1', 'testa');
        Setting::updateOrCreate('test2', 'testb');

        $this->assertSame(null, setting('test3'));
    }

    /** @test */
    public function setting_returns_a_default_when_a_setting_doesnt_exist()
    {
        Setting::updateOrCreate('test1', 'testa');
        Setting::updateOrCreate('test2', 'testb');

        $this->assertSame('default', setting('test3', 'default'));
    }

    /** @test */
    public function setting_exists_returns_true_if_a_setting_exists()
    {
        Setting::updateOrCreate('test1', 'testa');

        $this->assertTrue(setting_exists('test1'));
    }

    /** @test */
    public function setting_exists_returns_false_if_a_setting_exists()
    {
        Setting::updateOrCreate('test1', 'testa');

        $this->assertFalse(setting_exists('test2'));
    }

    /** @test */
    public function setting_missing_returns_false_if_a_setting_exists()
    {
        Setting::updateOrCreate('test1', 'testa');

        $this->assertFalse(setting_missing('test1'));
    }

    /** @test */
    public function setting_missing_returns_true_if_a_setting_exists()
    {
        Setting::updateOrCreate('test1', 'testa');

        $this->assertTrue(setting_missing('test2'));
    }

    /** @test */
    public function running_tests_is_true_if_running_tests()
    {
        $this->assertTrue(running_tests());
    }

    /** @test */
    public function running_tests_is_false_if_no_running_tests()
    {
        config()->set('app.running_tests', 0);

        $this->assertFalse(running_tests());
    }
}
