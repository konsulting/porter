<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;
use App\Support\Images\Organiser\Organiser;

class Pull extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:pull';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Pull the selected set of container images';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        app(Organiser::class)->pullImages();
    }
}
