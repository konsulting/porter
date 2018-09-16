<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Console\Cli;
use App\Support\Console\ConsoleWriter;
use App\Support\Console\ServerBag;
use App\Support\Mechanics\MacOs;
use Tests\BaseTestCase;

abstract class MechanicTestCase extends BaseTestCase
{
    protected $cli;
    protected $consoleWriter;
    protected $mechanicClass = MacOs::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->cli = \Mockery::mock(Cli::class);
        $this->consoleWriter = \Mockery::mock(ConsoleWriter::class);
    }

    protected function getMechanic($serverOverrides = [])
    {
        return new $this->mechanicClass($this->cli, $this->consoleWriter, new ServerBag($serverOverrides));
    }
}
