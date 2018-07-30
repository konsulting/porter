<?php

namespace App\Commands;

use App\Setting;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class Home extends Command
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

        $path = realpath($this->argument('path') ?: getcwd());

        $this->info('Setting home to ' . $path);

        Setting::where('name', 'home')->first()->update(['value' => $path]);
        Artisan::call('make-files');
    }
}
