<?php

namespace Tests\Unit\Support\Concerns;

use App\Porter;
use Mockery\MockInterface;

trait MocksPorter
{
    /** @var Porter|MockInterface */
    protected $porter;

    public function remakePorter()
    {
        $this->porter = \Mockery::mock(Porter::class);

        $this->app->extend(Porter::class, fn() => $this->porter);
    }
}
