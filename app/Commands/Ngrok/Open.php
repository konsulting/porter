<?php

namespace App\Commands\Ngrok;

use App\Commands\BaseCommand;
use App\Models\Site;
use App\Support\Mechanics\Exceptions\UnableToRetrieveIP;

class Open extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ngrok {site?} {--region=eu} {--no-inspection}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open ngrok connection to forward your dev environment to an external url';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectory($this->argument('site'));
        $wasSecure = false;

        if (!$site) {
            $this->error('No site at this location, and no site path provided.');

            return;
        }

        if (!$this->checkItWillResolveProperly()) {
            return;
        }

        if ($site->secure) {
            $this->info('Removing SSL for site (required for free ngrok version)');
            $site->unsecure();
            $wasSecure = true;
        }

        $this->porter->stop('ngrok');

        $tls = ' -bind-tls='.($wasSecure ? 'true' : 'false');
        $region = ' -region='.$this->option('region');
        $inspect = ' -inspect='.($this->option('no-inspection') ? 'false' : 'true');

        $this->dockerCompose
            ->runContainer('ngrok')
            ->append("ngrok http -host-header=rewrite{$region}{$tls}{$inspect} {$site->url}:80")
            ->interactive()
            ->perform();

        if ($wasSecure) {
            $this->info('Restoring SSL for site');
            $site->secure();
        }

        $this->porter->stop('ngrok');
    }

    /**
     * Checking that Porter is using the dns:set-host IP. If we don't ngrok
     * requests will only resolve to 127.0.0.1 which is internal to the
     * ngrok container, and results in a useless 502 error.
     *
     * @return bool
     */
    public function checkItWillResolveProperly()
    {
        try {
            if ($this->porterLibrary->getMechanic()->isUsingStandardLoopback()) {
                $this->info('You need to use an alternative loopback address.');
                $this->info('Please run porter dns:set-host and review the documentation here: https://github.com/konsulting/porter#dns');

                return false;
            }
        } catch (UnableToRetrieveIP $e) {
            $this->info('Please run porter dns:flush and try again. You may need to give it a little while.');

            return false;
        }

        return true;
    }
}
