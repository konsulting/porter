<?php

namespace App\Commands\Browser;

use App\Setting;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class Off extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browser:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Browser off';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_browser') == 'off') {
            return;
        }

        Setting::updateOrCreate('use_browser', 'off');

        Artisan::call('make-files');
    }
}
