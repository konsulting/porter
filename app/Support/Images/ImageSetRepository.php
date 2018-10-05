<?php

namespace App\Support\Images;

use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use Illuminate\Foundation\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImageSetRepository implements ImageSetRepositoryContract
{
    protected $locations = [];

    /**
     * ImageSetRepository constructor.
     * Accepts a set of locations, or a singular one.
     *
     * @param $location
     */
    public function __construct($location)
    {
        $this->addLocation($location);
    }

    /**
     * Add a location for the where we may find docker files.
     *
     * @param $location
     *
     * @return ImageSetRepository
     */
    public function addLocation($location)
    {
        if (!is_array($location)) {
            $location = [$location];
        }

        $this->locations = array_unique(array_merge($this->locations, $location));

        return $this;
    }

    /**
     * Get an image repository using the most recently added locations first.
     *
     * @param $imageSetName
     *
     * @throws \Exception
     *
     * @return ImageRepository
     */
    public function getImageRepository($imageSetName)
    {
        foreach (array_reverse($this->locations) as $location) {
            $path = $location.'/'.$imageSetName;
            if (is_dir($path)) {
                return new ImageRepository($path);
            }
        }

        throw new \Exception("Image Set {$imageSetName} not located.");
    }

    /**
     * Return a list of the available ImageSets.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableImageSets()
    {
        return collect($this->locations)
            ->flatMap(function ($location) {
                try {
                    return iterator_to_array(
                        Finder::create()->in($location)->depth(1)->directories()
                    );
                } catch (\InvalidArgumentException $e) {
                    return;
                }
            })->filter()
            ->map(function (SplFileInfo $directory) {
                return $directory->getRelativePathname();
            })->unique();
    }

    /**
     * Register the view namespaces for the image sets.
     *
     * @param $app
     */
    public function registerViewNamespaces(Application $app)
    {
        foreach ($this->availableImageSets() as $path => $namespace) {
            $app['view']->addNamespace($namespace, $path.'/docker_compose');
        }
    }
}
