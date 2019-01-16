<?php

namespace App\Commands\Php;

use App\Commands\BaseCommand;
use App\Models\PhpVersion;
use App\Models\Site;
use App\Support\Php\Xdebug;

class XdebugOff extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:xdebug-off {php_version?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn Xdebug off, optionally for a specific PHP version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $phpVersion = null;

        if ($this->argument('php_version')
            && ! ($phpVersion = PhpVersion::findByDirtyVersionNumber($this->argument('php_version')))
        ) {
            $this->error('Invalid PHP version provided');

            return;
        }

        app(Xdebug::class)->turnOff($phpVersion);

        $this->porter->restart($phpVersion ? $phpVersion->fpm_name : null);
    }
}
