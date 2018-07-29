<?php

namespace App\Commands\MySql;

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
    protected $signature = 'mysql:off';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn mysql off';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (setting('use_mysql') == 'off') {
            return;
        }

        Setting::where('name', 'use_mysql')->update(['value' => 'off']);

        Artisan::call('make-files');
    }
}
