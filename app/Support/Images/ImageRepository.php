<?php

namespace App\Support\Images;

use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImageRepository implements ImageRepositoryContract
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $name;

    /**
     * ImageRepository constructor.
     *
     * @param $path
     * @param $name
     */
    public function __construct($path, $name)
    {
        $this->path = $path;
        $this->name = $name;
    }

    /**
     * Get the docker images that are pulled on install. A custom image set name may be specified.
     *
     * @throws Exception
     *
     * @return array
     */
    public function firstParty()
    {
        $images = [];

        foreach ((new Finder())->in($this->path)->directories()->sortByName() as $directory) {
            /* @var $directory \Symfony\Component\Finder\SplFileInfo */
            $images[] = new Image($this->getImageName($directory, $this->name), $directory->getRealPath());
        }

        return $images;
    }

    /**
     * The third party docker images.
     *
     * @return array
     */
    public function thirdParty()
    {
        return [
            new Image('mysql:5.7'),
            new Image('redis:alpine'),
            new Image('andyshinn/dnsmasq'),
            new Image('mailhog/mailhog:v1.0.0'),
        ];
    }

    /**
     * Return a full listing of images.
     *
     * @throws Exception
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->firstParty(), $this->thirdParty());
    }

    /**
     * Return the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Find the image for a given service.
     *
     * @param $service
     * @param bool $firstParty
     *
     * @throws Exception
     *
     * @return array
     */
    public function findByServiceName($service, $firstParty = false)
    {
        $service = preg_replace('/[^a-zA-Z0-9\-\_]/', '-', $service);
        $images = $firstParty ? $this->firstParty() : $this->all();

        if (!$service) {
            return $images;
        }

        return array_values(array_filter($images, function (Image $image) use ($service) {
            return strpos($image->getName(), $service) !== false;
        }));
    }

    /**
     * Get the name of the docker image from the directory and image set name.
     *
     * @param string      $imageSetName
     * @param SplFileInfo $dir
     *
     * @return string
     */
    private function getImageName(SplFileInfo $dir, $imageSetName)
    {
        return $imageSetName.'-'.$dir->getFileName().':latest';
    }
}
