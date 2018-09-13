<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Console\ServerBag;
use App\Support\Mechanics\Windows;
use Tests\BaseTestCase;

class WindowsBaseTest extends BaseTestCase
{
    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->app->instance(ServerBag::class, new ServerBag(['HOME'=>'C:\Users\keoghan']));

        $this->assertEquals('C:\Users\keoghan', app(Windows::class)->getUserHomePath());
    }
}
