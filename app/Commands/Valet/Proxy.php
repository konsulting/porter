<?php

namespace App\Commands\Valet;

use App\Commands\BaseCommand;
use App\Models\Site;
use App\Support\Valet\Valet;

class Proxy extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'valet:proxy {site?} {--all}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Manually add a Valet proxy for a site';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $sites = $this->option('all')
            ? Site::all()
            : [Site::resolveFromPathOrCurrentWorkingDirectoryOrFail((string) $this->argument('site'))];

        foreach ($sites as $site) {
            app(Valet::class)->addSite($site);
        }
    }
}
