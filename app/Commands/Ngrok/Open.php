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
     * Was the site secure at the start of the command?
     *
     * @var bool
     */
    protected $wasSecure = false;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectory($this->argument('site'));

        if (!$site) {
            $this->error('No site at this location, and no site path provided.');
            return;
        }

        if (!$this->checkItWillResolveProperly()) {
            return;
        }

        $this->removeSSLIfNeeded($site);

        $this->porter->stop('ngrok');

        $this->dockerCompose
            ->runContainer('ngrok')
            ->append($this->constructNgrokCommand($site))
            ->interactive()
            ->perform();

        $this->restoreSSLIfNeeded($site);

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

    /**
     * Remove SSL from the site if it was secured.
     *
     * @param  Site $site
     */
    protected function removeSSLIfNeeded(Site $site): void
    {
        if (! $site->secure) {
            return;
        }

        $this->info('Removing SSL for site (required for free ngrok version)');
        $this->wasSecure = true;
        $site->unsecure();
    }

    /**
     * Add SSL back to the site if it was previously secured
     *
     * @param  Site  $site
     */
    protected function restoreSSLIfNeeded(Site $site): void
    {
        if (! $this->wasSecure) {
            return;
        }

        $this->info('Restoring SSL for site');
        $site->secure();
    }

    /**
     * Construct the ngrok command
     *
     * @param  Site  $site
     *
     * @return string
     */
    protected function constructNgrokCommand(Site $site): string
    {
        $tls = ' -bind-tls='.($this->wasSecure ? 'true' : 'false');
        $region = ' -region='.$this->option('region');
        $inspect = ' -inspect='.($this->option('no-inspection') ? 'false' : 'true');

        return "ngrok http -host-header=rewrite{$region}{$tls}{$inspect} {$site->url}:80";
    }
}
