<?php

namespace App\Commands\MySql;

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
    protected $signature = 'mysql:on';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn mysql on';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_mysql') == 'on') {
            return;
        }

        Setting::where('name', 'use_mysql')->update(['value' => 'on']);

        Artisan::call('make-files');
    }
}
