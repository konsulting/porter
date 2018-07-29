<?php

namespace App\Commands;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class Begin extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'begin {--force}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run initial seeders and set up';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $force = $this->option('force');
        $home = getcwd();

        if (! $force && DB::table('migrations')->count() > 0) {
            $this->error('Already began. If you definitely want to continue, you can force with the --force flag.');

            return;
        }

        $this->call('vendor:publish', ['--provider' => AppServiceProvider::class]);
        $this->call('migrate:fresh', ['--seed' => true]);
        $this->call('sites:home', [$home]);
    }
}
