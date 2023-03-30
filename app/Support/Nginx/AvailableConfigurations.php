<?php

namespace App\Support\Nginx;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AvailableConfigurations
{
    protected $locations;

    /**
     * AvailableConfigurations constructor.
     *
     * @param array|null $locations
     */
    public function __construct($locations = null)
    {
        $this->locations = $locations ?: $this->locationsFromViewFinder();
    }

    /**
     * Get the list of conf files, indicating current.
     *
     *
     * @return mixed
     */
    public function getList(?string $highlight = null)
    {
        return $this->getFileNames()
            ->mapWithKeys(fn($name) => [$name => $name.($name == $highlight ? ' (current)' : '')])->toArray();
    }

    /**
     * Return the locations being used.
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Retrieve the locations of NGiNX configurations.
     *
     * @return array
     */
    protected function locationsFromViewFinder()
    {
        return collect(view()->getFinder()->getPaths())
            ->map(fn($location) => $location.'/nginx')->toArray();
    }

    /**
     * Scour the locations and get a Collection of NGiNX files.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getFileNames()
    {
        return collect(iterator_to_array(
            Finder::create()->in($this->locations)->directories()
        ))->map(fn(SplFileInfo $file) => $file->getFilename())->sort();
    }
}
