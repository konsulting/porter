<?php

namespace Tests\Unit\Support\Concerns;

use App\Support\Console\DockerCompose\CliCommandFactory;
use Mockery\MockInterface;

trait MocksDockerCompose
{
    /** @var CliCommandFactory|MockInterface */
    protected $dockerCompose;

    public function mockDockerCompose()
    {
        $this->dockerCompose = \Mockery::mock(CliCommandFactory::class);

        $this->app->extend(CliCommandFactory::class, fn() => $this->dockerCompose);
    }
}
