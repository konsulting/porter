<?php

namespace App\Commands\Mutagen;

use App\Commands\BaseCommand;
use App\Support\Mutagen\Mutagen;

class Install extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mutagen:install';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install Mutagen (mac only)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Mutagen::class)->install();
    }
}
