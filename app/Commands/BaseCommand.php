<?php

namespace App\Commands;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    protected $porter;

    public function __construct(Porter $porter)
    {
        parent::__construct();

        $this->porter = $porter;
    }
}
