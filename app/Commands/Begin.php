<?php

namespace App\Commands;

use App\Exceptions\PorterSetupFailed;
use App\Support\Images\Organiser\Organiser as ImageOrganiser;

class Begin extends BaseCommand
{
    /**
     * The begin command may be run at any time, since it's needed to perform setup.
     *
     * @var bool
     */
    protected $porterMustBeSetUp = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'begin 
        {home? : The path of Porter\'s home directory (optional). Defaults to current directory.} 
        {--force : Force setup to run, even if it has already been run}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run initial seeders and set up';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('================');
        $this->line('PREPARING PORTER');
        $this->line('================');
        $this->line('');

        try {
            $this->porterLibrary->setup(/* @scrutinizer ignore-type */$this->app, (bool) $this->option('force'));
        } catch (PorterSetupFailed $e) {
            $this->alert($e->getMessage());

            return;
        }

        $this->info('Your Porter settings are stored in '.$this->porterLibrary->path());
        $this->info('');

        $this->setHomeDirectory();

        $this->info('Retrieving docker images');
        app(ImageOrganiser::class)->pullImages();
    }

    /**
     * Set the Porter home directory, prompting for input if necessary.
     *
     * @return void
     */
    protected function setHomeDirectory()
    {
        if ($this->argument('home') || $this->option('no-interaction')) {
            $home = realpath((string) $this->argument('home') ?: $this->cli->currentWorkingDirectory());
        } else {
            $this->comment('Please enter the root directory for your sites, or leave blank to use the current directory.');
            $home = $this->ask('', $this->cli->currentWorkingDirectory());
        }

        $this->info("Setting home to {$home}.");
        $this->comment('This is the root directory for your sites.');
        $this->comment("If this is incorrect, you can change it using the 'porter home' command.");
        $this->comment('');

        $this->callSilent('home', ['path' => $home]);
    }
}
