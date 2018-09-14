<?php

namespace App\Commands;

use App\Models\Setting;

class Home extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'home {path?} {--show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the root directory for Porter sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->option('show')) {
            $this->info("Home is currently: ". setting('home'));
            return;
        }

        $path = realpath($this->argument('path') ?: $this->cli->currentWorkingDirectory());

        $this->info('Setting home to ' . $path);

        Setting::updateOrCreate('home', $path);
        $this->call('make-files');
    }
}
