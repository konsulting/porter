<?php

namespace Tests\Unit\Commands;

use App\Commands\MakeFiles;
use App\Models\Setting;
use Tests\BaseTestCase;

class HomeTest extends BaseTestCase
{
    /** @test */
    public function it_outputs_the_current_home(): void
    {
        Setting::updateOrCreate('home', 'home');

        $this->artisan('home', ['--show' => true])
            ->expectsOutput('Home is currently: home');
    }

    /** @test */
    public function it_updates_home_to_a_given_path()
    {
        $this->mockPorterCommand(MakeFiles::class);

        Setting::updateOrCreate('home', 'old_home');
        $home = base_path('tests/TestWebRoot');

        $this->artisan('home', ['path' => $home])
            ->expectsOutput('Setting home to '.$home);

        $this->assertEquals($home, setting('home'));
    }
}
