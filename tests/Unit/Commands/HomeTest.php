<?php

namespace Tests\Unit\Commands;

use App\Commands\MakeFiles;
use App\Models\Setting;
use App\Support\Dnsmasq\Config;
use Illuminate\Support\Facades\Artisan;
use Tests\BaseTestCase;

class HomeTest extends BaseTestCase
{
    /** @test */
    public function it_outputs_the_current_home()
    {
        Setting::updateOrCreate('home', 'home');

        $this->artisan('home', ['--show' => true]);

        $this->assertRegExp('/Home is currently: home/', Artisan::output());
    }

    /** @test */
    public function it_updates_home_to_a_given_path()
    {
        $this->mockPorterCommand(MakeFiles::class);

        Setting::updateOrCreate('home', 'old_home');

        $this->artisan('home', ['path' => '/Users/keoghan/Code']);

        $this->assertRegExp('/Setting home to \/Users\/keoghan\/Code/', Artisan::output());

        $this->assertEquals('/Users/keoghan/Code', setting('home'));
    }
}
