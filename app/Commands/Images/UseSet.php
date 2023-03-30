<?php

namespace App\Commands\Images;

use App\Commands\BaseCommand;
use App\Models\Setting;
use App\Support\Contracts\ImageSetRepository;
use Illuminate\Support\Facades\Artisan;

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
     */
    public function handle(): void
    {
        $current = $this->porter->getDockerImageSet()->getName();

        if ($this->option('show')) {
            $this->info('The current image set is: '.$current);

            return;
        }

        $sets = app(ImageSetRepository::class)
            ->availableImageSets()
            ->mapWithKeys(fn($set) => [$set => $set.($current == $set ? ' (current)' : '')])->toArray();

        $option = $this->menu(
            'Available Image Sets',
            $sets
        )->open();

        if (!$option) {
            return;
        }

        Setting::updateOrCreate('docker_image_set', $option);

        Artisan::call('make-files');
    }
}
