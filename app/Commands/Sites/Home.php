<?php

namespace App\Commands\Sites;

use Illuminate\Console\Scheduling\Schedule;
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

        app(Porter::class)->changeSetting('path', $path);
    }
}
