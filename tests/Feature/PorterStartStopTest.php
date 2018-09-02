<?php

namespace Tests\Feature;

use App\Porter;
use Illuminate\Support\Facades\Artisan;

class PorterStartStopTest extends TestCase
{
    protected $porter;

    public function setUp() :void
    {
        parent::setUp();

        $this->porter = app(Porter::class);
    }

    /**
     * @test
     * @group docker
     */
    public function porter_can_be_started_and_stopped()
    {
        Artisan::call('begin', ['home' => __DIR__.'/../TestWebRoot']);

        $this->assertFalse($this->porter->isUp());

        Artisan::call('start');

        $this->assertTrue($this->porter->isUp());

        Artisan::call('stop');
    }
}
