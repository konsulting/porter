<?php

namespace App\Support\Contracts;

interface ImageRepository
{
    /**
     * Get the docker images that are pulled on install. A custom image set name may be specified.
     *
     * @param string $imageSetName
     * @return array
     */
    public function firstParty($imageSetName);

    /**
     * The third party docker images.
     *
     * @return array
     */
    public function thirdParty();
}
