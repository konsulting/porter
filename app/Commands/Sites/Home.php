<?php

namespace App\Commands\Sites;

use App\Setting;
use LaravelZero\Framework\Commands\Command;
use App\Porter;

class Home extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:home {path?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the home directory for Porter sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $path = $this->argument('path') ?: getcwd();

        Setting::firstOrCreate(['name' => 'home'], ['value' => $path]);
    }
}
