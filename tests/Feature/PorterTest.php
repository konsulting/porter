<?php

namespace Tests\Feature;

use App\Porter;
use Illuminate\Support\Facades\Artisan;

class PorterTest extends TestCase
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
        $this->preparePorter();

        $this->assertFalse($this->porter->isUp());

        Artisan::call('start');

        $this->assertTrue($this->porter->isUp());

        Artisan::call('stop');
    }

    public function preparePorter()
    {
        Artisan::call('begin');

        Artisan::call('home', ['path' => __DIR__.'/../TestWebRoot']);

        Artisan::call('make-files');
    }
}
