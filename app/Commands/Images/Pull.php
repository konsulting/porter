<?php

namespace App\Commands\Images;


use App\Commands\BaseCommand;

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
     */
    public function handle(): void
    {
        $this->porter->pullImages();
    }
}
