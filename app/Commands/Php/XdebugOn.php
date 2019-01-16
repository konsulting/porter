<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;
use App\Support\Php\Xdebug;

class XdebugOn extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:xdebug-on {php_version?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Xdebug on, optionally for a specific PHP version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $phpVersion = null;

        if ($this->argument('php_version')
            && !($phpVersion = PhpVersion::findByDirtyVersionNumber($this->argument('php_version')))
        ) {
            $this->error('Invalid PHP version provided');

            return;
        }

        app(Xdebug::class)->turnOn($phpVersion);

        $this->porter->restart($phpVersion ? $phpVersion->fpm_name : null);
    }
}
