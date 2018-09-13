<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Console\ServerBag;
use App\Support\Mechanics\Linux;
use Tests\BaseTestCase;

class LinuxBaseTest extends BaseTestCase
{
    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->app->instance(ServerBag::class, new ServerBag(['HOME'=>'/home/keoghan']));

        $this->assertEquals('/home/keoghan', app(Linux::class)->getUserHomePath());
    }
}
