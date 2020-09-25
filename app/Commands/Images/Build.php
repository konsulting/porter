<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;
use App\Support\Images\Organiser\Organiser;

class Build extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:build {service?} {--fresh}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build the selected set of container images';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        app(Organiser::class)->buildImages((string) $this->argument('service'), (string) $this->option('fresh'));
    }
}
