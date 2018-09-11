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
    protected $signature = 'begin {home?} {--force}';

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
        $home = realpath($this->argument('home') ?: $this->cli->currentWorkingDirectory());

        if (! $force && Database::exists()) {
            $this->error("Already began, so we've stopped to avoid wiping your settings.");
            $this->error("If you definitely want to continue, you can force with the --force flag.");
            return;
        }

        $this->line("================");
        $this->line("PREPARING PORTER");
        $this->line("================");
        $this->line("");

        mkdir(config('porter.library_path'));

        $this->callSilent('vendor:publish', ['--provider' => AppServiceProvider::class]);

        Database::ensureExists($force);

        $this->info("Your Porter settings are stored in ".config('porter.library_path'));
        $this->info("");

        $this->callSilent('home', ['path' => $home]);

        $this->info("Setting home to {$home}.");
        $this->comment("This is the used as the root directory for your sites.");
        $this->comment("If this is incorrect, you can change it using the 'porter home' command.");
        $this->comment("");

        $this->info("Retrieving docker images");
        $this->porter->pullImages();
    }
}
