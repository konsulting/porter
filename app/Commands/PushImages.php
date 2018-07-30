<?php

namespace App\Commands;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class PushImages extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'push-images';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Push all our container images';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->pushImages();
    }
}
