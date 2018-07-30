<?php

namespace App\Commands;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class BuildImages extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build-images';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build all our container images';

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
