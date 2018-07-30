<?php

namespace App\Commands;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class PullImages extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'pull-images';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Pull all the container images';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->pullImages();
    }
}
