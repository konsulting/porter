<?php

namespace App\Commands\Sites;

use App\PhpVersion;
use App\Setting;
use App\Site;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class DefaultPhp extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:default-php';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the default php version for Porter.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $option = $this->menu(
            'Available PHP versions',
            PhpVersion::pluck('version_number', 'id')->toArray()
        )->open();

        if (! $option) {
            return;
        }

        PhpVersion::setDefaultVersion($option);

        Artisan::call('make-files');
    }
}
