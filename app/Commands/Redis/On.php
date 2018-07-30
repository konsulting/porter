<?php

namespace App\Commands\Redis;

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
    protected $signature = 'redis:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Redis on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_redis') == 'on') {
            return;
        }

        Setting::where('name', 'use_redis')->update(['value' => 'on']);

        Artisan::call('make-files');
    }
}
