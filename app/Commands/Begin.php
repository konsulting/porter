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
        $this->line('================');
        $this->line('PREPARING PORTER');
        $this->line('================');
        $this->line('');

        try {
            $this->porterLibrary->setup($this->app, (bool) $this->option('force'));
        } catch (PorterSetupFailed $e) {
            $this->alert($e->getMessage());

            return;
        }

        $this->info('Your Porter settings are stored in '.$this->porterLibrary->path());
        $this->info('');

        $home = realpath((string) $this->argument('home') ?: $this->cli->currentWorkingDirectory());
        $this->callSilent('home', ['path' => $home]);

        $this->info("Setting home to {$home}.");
        $this->comment('This is the root directory for your sites.');
        $this->comment("If this is incorrect, you can change it using the 'porter home' command.");
        $this->comment('');

        $this->info('Retrieving docker images');
        (new ImageOrganiser($this->porter->getDockerImageSet(), $this->cli));
    }
}
