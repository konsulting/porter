<?php

namespace App\Commands\Site;

use App\Commands\BaseCommand;
use App\Models\Site;
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
     * @throws \Exception
     */
    public function handle(): void
    {
        $site = Site::resolveFromPathOrCurrentWorkingDirectoryOrFail($this->argument('site'));

        $currentNginxConf = $site->nginx_conf;

        $nginxFileLocations = collect(view()->getFinder()->getPaths())
            ->map(function ($location) {
                return $location.'/nginx';
            })->toArray();

        $types = collect(iterator_to_array(
            Finder::create()
                ->in($nginxFileLocations)
                ->directories()
        ))->mapWithKeys(function (\SplFileInfo $file) use ($currentNginxConf) {
            $conf = $file->getFilename();
            return [$conf => $conf . ($conf == $currentNginxConf ? ' (current)' : '')];
        })->sort()->toArray();

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
