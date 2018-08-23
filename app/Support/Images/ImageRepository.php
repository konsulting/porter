<?php

namespace App\Support\Images;

use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImageRepository implements ImageRepositoryContract
{
    /**
     * Get the docker images that are pulled on install. A custom image set name may be specified.
     *
     * @param string $imageSetName
     * @return array
     */
    public function firstParty($imageSetName)
    {
        $images = [];
        $imagesDir = base_path('docker/' . $imageSetName);

        foreach ((new Finder)->in($imagesDir)->directories() as $directory) {
            $images[] = $this->getImageName($directory, $imageSetName);
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
            'mysql:5.7',
            'redis:alpine',
            'andyshinn/dnsmasq',
            'mailhog/mailhog:v1.0.0',
        ];
    }

    /**
     * Get the name of the docker image from the directory and image set name.
     *
     * @param string $imageSetName
     * @param SplFileInfo $dir
     * @return string
     */
    private function getImageName(SplFileInfo $dir, $imageSetName)
    {
        return $imageSetName . '-' . $dir->getFileName() . ':latest';
    }
}
