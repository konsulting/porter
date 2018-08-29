<?php

namespace App\Commands;

use App\Providers\AppServiceProvider;
use App\Support\Database\Database;

class Begin extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'begin {--force}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run initial seeders and set up';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $force = $this->option('force');
        $home = getcwd();

        if (! $force && Database::exists()) {
            $this->error('Already began. If you definitely want to continue, you can force with the --force flag.');

            return;
        }

        Database::ensureExists($force);

        $this->call('vendor:publish', ['--provider' => AppServiceProvider::class]);
        $this->call('home', [$home]);

        $this->porter->pullImages();
    }
}
