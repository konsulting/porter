<?php

namespace Tests\Unit\Commands;

use App\Support\Images\Organiser\Organiser;
use Illuminate\Support\Facades\Artisan;
use Tests\BaseTestCase;

class BeginTest extends BaseTestCase
{
    protected $organiser;

    public function setUp(): void
    {
        parent::setup();

        $this->organiser = \Mockery::mock(Organiser::class);
        $this->organiser->expects('pullImages');

        $this->afterApplicationCreated(function () {
            app()->bind(Organiser::class, function () {
                return $this->organiser;
            });
        });
    }

    /** @test */
    public function it_prompts_for_the_home_directory()
    {
        $this->artisan('begin', ['--force' => true]);

        $expected = 'Please enter the root directory for your sites, or leave blank to use the current directory.';
        $this->stringContains($expected)->evaluate(Artisan::output());
    }

    /** @test */
    public function it_uses_the_supplied_home_directory()
    {
        $home = storage_path('temp/test_home');
        file_exists($home) ? null : mkdir($home, 0777, true);

        $this->artisan('begin', ['--force' => true, 'home' => $home]);

        $expected = "Setting home to {$home}";
        $this->stringContains($expected)->evaluate(Artisan::output());
    }

    /** @test */
    public function it_uses_the_current_directory_if_no_interaction()
    {
        $this->artisan('begin', ['--force' => true, '--no-interaction' => true]);

        $expected = 'Setting home to '.base_path();
        $this->stringContains($expected)->evaluate(Artisan::output());
    }
}
