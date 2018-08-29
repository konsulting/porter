<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Site;
use Symfony\Component\Finder\Finder;

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
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);
        $currentNginxConf = $site->nginx_conf;

        $types = collect(iterator_to_array(
            Finder::create()
                ->in(resource_path('views/nginx'))
                ->sortByName()
                ->directories()
        ))->mapWithKeys(function (\SplFileInfo $file) use ($currentNginxConf) {
            $conf = $file->getFilename();
            return [$conf => $conf . ($conf == $currentNginxConf ? ' (current)' : '')];
        })->toArray();


        $option = $this->menu(
            'Available Nginx Types',
            $types
        )->open();

        if (! $option) {
            return;
        }

        $site->setNginxType($option);
    }
}
