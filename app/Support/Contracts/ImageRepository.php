<?php

namespace App\Support\Contracts;

use App\Support\Images\Image;

interface ImageRepository
{
    /**
     * Get the docker images that are pulled on install. A custom image set name may be specified.
     *
     * @return array
     */
    public function firstParty();

    /**
     * The third party docker images.
     *
     * @return array
     */
    public function thirdParty();

    /**
     * Return a full listing of images.
     *
     * @return array
     */
    public function all();

    /**
     * Return the Docker context path.
     *
     * @return mixed
     */
    public function getDockerContext();

    /**
     * Return the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Find the image for a given service.
     *
     * @param $service
     * @param bool $firstPartyOnly
     *
     * @return array
     */
    public function findByServiceName($service, $firstPartyOnly = false);

    /**
     * Find the image for a given service.
     *
     * @param $service
     * @param bool $firstPartyOnly
     *
     * @return Image
     */
    public function firstByServiceName($service, $firstPartyOnly = false);

    /**
     * Return the name.
     *
     * @return string
     */
    public function getName();
}
