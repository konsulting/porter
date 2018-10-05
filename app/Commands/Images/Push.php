<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;
use App\Support\Images\Organiser\Organiser;

class Push extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:push';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Push the selected set of container images';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        app(Organiser::class)->pushImages();
    }
}
