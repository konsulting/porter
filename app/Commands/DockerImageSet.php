<?php

namespace App\Commands;

use App\Porter;
use App\Providers\AppServiceProvider;
use App\Setting;
use App\Support\Database\Database;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\Finder;

class DockerImageSet extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'docker-image-set {--show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Change the image set to use';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $current = app(Porter::class)->getDockerImageSet();

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
