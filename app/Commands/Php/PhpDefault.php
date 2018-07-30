<?php

namespace App\Commands\Php;

use App\PhpVersion;
use App\Setting;
use App\Site;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class PhpDefault extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:default';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the default PHP version';

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
