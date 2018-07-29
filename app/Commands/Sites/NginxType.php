<?php

namespace App\Commands\Sites;

use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\Finder;

class NginxType extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:nginx-type {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the nginx config for a site.';

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

        $types = collect(iterator_to_array(
            Finder::create()
            ->in(resource_path('views/nginx'))
            ->directories()

        ))->mapWithKeys(function (\SplFileInfo $file) {
            return [$file->getFilename() => $file->getFilename()];
        })->toArray();

        $site = Site::firstOrCreateForName($name);

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
