<?php

namespace App\Support\Images;

use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use Exception;

class ImageRepository implements ImageRepositoryContract
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $name;

    protected $firstPartyImages = [];

    protected $thirdPartyImages = [];

    /**
     * ImageRepository constructor.
     *
     * @param $path
     *
     * @throws Exception
     */
    public function __construct($path)
    {
        $this->path = $path;

        $this->loadConfig();
    }

    /**
     * Load the configuration file for the image set.
     *
     * @throws Exception
     */
    protected function loadConfig()
    {
        try {
            $config = json_decode(file_get_contents($this->path.'/config.json'));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }

            if (!property_exists($config, 'name') || !$config->name) {
                throw new \Exception('There is no name specified.');
            }

            $this->name = $config->name;
            $this->firstPartyImages = (array) $config->firstParty ?? [];
            $this->thirdPartyImages = (array) $config->thirdParty ?? [];
        } catch (\Exception $e) {
            throw new \Exception("Failed loading config for image set '{$this->path}'. {$e->getMessage()}");
        }
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
        return collect($this->firstPartyImages)
            ->map(function ($version, $name) {
                return new Image($this->name.'-'.$name.':'.$version, $this->getDockerContext().$name);
            })->values()->toArray();
    }

    /**
     * The third party docker images.
     *
     * @return array
     */
    public function thirdParty()
    {
        return collect($this->thirdPartyImages)
            ->map(function ($image) {
                return new Image($image);
            })->toArray();
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
     * Return Docker context path.
     *
     * @return string
     */
    public function getDockerContext()
    {
        return $this->path.'/docker/';
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
     * @param bool $firstPartyOnly
     *
     * @throws Exception
     *
     * @return array
     */
    public function findByServiceName($service, $firstPartyOnly = false)
    {
        $service = preg_replace('/[^a-zA-Z0-9\-\_]/', '-', $service);
        $images = $firstPartyOnly ? $this->firstParty() : $this->all();

        if (!$service) {
            return $images;
        }

        return array_values(array_filter($images, function (Image $image) use ($service) {
            return strpos($image->getName(), $service) !== false;
        }));
    }
}
