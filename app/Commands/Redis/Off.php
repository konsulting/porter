<?php

namespace App\Commands\Redis;

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
    protected $signature = 'redis:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Redis off';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_redis') == 'off') {
            return;
        }

        Setting::updateOrCreate('use_redis', 'off');

        Artisan::call('make-files');
    }
}
