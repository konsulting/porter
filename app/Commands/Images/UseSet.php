<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;
use App\Setting;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Finder\Finder;

class UseSet extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:set {--show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Change the set of container images use';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $current = $this->porter->getDockerImageSet();

        if ($this->option('show')) {
            $this->info("The current image set is: ".$current);

            return;
        }

        $sets = $this->findNamespaces()
            ->mapWithKeys(function (\SplFileInfo $set) use ($current) {
                $name = str_replace(base_path('docker').'/', '', $set->getRealPath());

                return [$name => $name . ($current == $name ? ' (current)' : '')];
            })->toArray();

        $option = $this->menu(
            'Available Image Sets',
            $sets
        )->open();

        if (! $option) {
            return;
        }

        Setting::updateOrCreate('docker_image_set', $option);

        Artisan::call('make-files');
    }

    protected function findNamespaces()
    {
        return collect(iterator_to_array(Finder::create()->in(base_path('docker'))->depth(1)->directories()));
    }
}
