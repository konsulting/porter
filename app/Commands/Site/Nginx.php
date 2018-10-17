<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;
use App\Support\Nginx\AvailableConfigurations;

class Nginx extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:nginx {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Choose the NGiNX config template for a site';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectoryOrFail((string) $this->argument('site'));

        $option = $this->menu(
            'Available Nginx Types',
            (new AvailableConfigurations())->getList($site->nginx_conf)
        )->open();

        if (!$option) {
            return;
        }

        $site->setNginxType($option);
    }
}
