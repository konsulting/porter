<?php

namespace App\Commands;

use App\Setting;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class Tld extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tld {tld?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the tld for Porter sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $tld = $this->argument('tld');

        if (! $tld) {
            $this->info(sprintf("The current tld is '%s'", setting('tld')));
            return;
        }

        Setting::where('name', 'tld')->first()->update(['value' => $tld]);

        Artisan::call('make-files');
    }
}
