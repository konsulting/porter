<?php

namespace App\Support\Contracts;

interface ImageSetRepository
{
    /**
     * Add a location for the where we may find docker files.
     *
     * @param $location
     */
    public function addLocation($location);

    /**
     * Get an image repository using the most recently added locations first.
     *
     * @param $imageSetName
     *
     * @return ImageRepository
     */
    public function getImageRepository($imageSetName);

    /**
     * Return a list of the available ImageSets.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableImageSets();
}
