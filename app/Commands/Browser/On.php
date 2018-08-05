<?php

namespace App\Commands\Browser;

use App\Setting;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class On extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browser:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Browser on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_browser') == 'on') {
            return;
        }

        Setting::updateOrCreate('use_browser', 'on');

        Artisan::call('make-files');
    }
}
