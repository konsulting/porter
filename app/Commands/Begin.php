<?php

namespace App\Commands;

use App\Support\FilePublisher;
use App\Support\Database\Database;
use App\Support\Mechanics\ChooseMechanic;

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

        if (! config('porter.library_path')) {
            $libraryPath = ChooseMechanic::forOS()->getUserHomePath().'/.porter';

            $this->app->make(FilePublisher::class)->publish(
                base_path('.env.example'),
                base_path('.env')
            );

            $envContent = $this->app['files']->get(base_path('.env'));
            $envContent = preg_replace('/LIBRARY_PATH=.*\n/', "LIBRARY_PATH=\"{$libraryPath}\"\n", $envContent);
            $this->app['files']->put(base_path('.env'), $envContent);

            $this->app['config']->set('database.connection.default.database', $libraryPath.'/database.sqlite');
            $this->app['config']->set('porter.library_path', $libraryPath);
            $this->app['config']->set('porter.docker-compose-file', $libraryPath.'/docker-compose.yaml');
        }

        if (! config('porter.library_path')) {
            $this->error('Failed detecting and setting the library path for Porter');
            die();
        }

        if (! $force && Database::exists()) {
            $this->error("Already began, so we've stopped to avoid wiping your settings.");
            $this->error("If you definitely want to continue, you can force with the --force flag.");
            return;
        }

        $this->line("================");
        $this->line("PREPARING PORTER");
        $this->line("================");
        $this->line("");

        $this->app->make(FilePublisher::class)->publish(
            resource_path('stubs/config'),
            config('porter.library_path').'/config'
        );

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
