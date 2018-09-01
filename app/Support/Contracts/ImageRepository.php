<?php

namespace App\Support\Contracts;

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
     * Return a full listing of images
     *
     * @return array
     */
    public function all();

    /**
     * Return the path
     *
     * @return string
     */
    public function getPath();

    /**
     * Return the name
     *
     * @return string
     */
    public function getName();
}
