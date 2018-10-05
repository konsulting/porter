<?php

namespace Tests\Unit\Commands;

use App\Support\Images\Organiser\Organiser;
use Tests\BaseTestCase;

class BeginTest extends BaseTestCase
{
    protected $organiser;

    public function setUp() : void {
        $this->organiser = \Mockery::mock(Organiser::class);

        $this->afterApplicationCreated(function () {
            app()->bind(Organiser::class, function () {
                return $this->organiser;
            });
        });

        parent::setup();
    }

    /** @test */
    public function it_pulls_the_docker_images()
    {
        $this->organiser->expects('pullImages');

        $this->artisan('begin', ['--force' => true]);
    }
}
