<?php

namespace App\Commands\Ngrok;

use App\Porter;
use App\Models\Site;
use App\Commands\BaseCommand;

class Open extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ngrok';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open Node cli';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectory();
        $wasSecure = false;

        if ($site->secure) {
            $this->info('Removing SSL for site (required for free ngrok version)');
            $site->unsecure();
            $wasSecure = true;
        }

        app(Porter::class)->stop('ngrok');

        $this->dockerCompose
            ->runContainer('ngrok')
            ->append('ngrok http -host-header=rewrite -region=eu -bind-tls='.($wasSecure ? 'true' : 'false').' -inspect=false '.$site->url.':80')
            ->interactive()
            ->perform();

        if ($wasSecure) {
            $this->info('Restoring SSL for site');
            $site->secure();
        }

        app(Porter::class)->stop('ngrok');
    }
}
