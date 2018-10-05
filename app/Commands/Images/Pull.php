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
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        (new Organiser($this->porter->getDockerImageSet(), $this->cli))->pullImages();
    }
}
