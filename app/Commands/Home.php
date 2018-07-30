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
    protected $signature = 'home {path?}';

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
        $path = realpath($this->argument('path') ?: getcwd());

        Setting::where('name', 'home')->first()->update(['value' => $path]);

        Artisan::call('make-files');
    }
}
