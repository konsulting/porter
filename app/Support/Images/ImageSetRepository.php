<?php

namespace App\Support\Images;

use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use Symfony\Component\Finder\Finder;

class ImageSetRepository implements ImageSetRepositoryContract
{
    protected $locations;

    public function __construct()
    {
        $this->addLocation(base_path('docker'));
    }

    /**
     * Add a location for the where we may find docker files
     *
     * @param $location
     * @return ImageSetRepository
     */
    public function addLocation($location)
    {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Get an image repository using the most recently added locations first
     *
     * @param $imageSetName
     * @return string
     * @throws \Exception
     */
    public function getImageRepository($imageSetName)
    {
        foreach (array_reverse($this->locations) as $location) {
            $path = $location.'/'.$imageSetName;

            if (is_dir($path)) {
                return new ImageRepository($path, $imageSetName);
            }
        }

        throw new \Exception("Image Set {$imageSetName} not located.");
    }

    /**
     * Return a list of the available ImageSets
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableImageSets()
    {
        return collect($this->locations)
            ->flatMap(function ($location) {
                return iterator_to_array(
                    Finder::create()->in($location)->depth(1)->directories()
                );
            })->map(function (\SplFileInfo $directory) {
                return $directory->getFilename();
            })->unique();
    }
}
