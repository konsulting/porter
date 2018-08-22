<?php

namespace App\Commands\Images;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class Build extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:build';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build the selected set of container images';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->buildImages();
    }
}
