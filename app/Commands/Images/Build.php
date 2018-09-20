<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;

class Build extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:build {service?}';

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
        $this->porter->buildImages((string) $this->argument('service'));
    }
}
