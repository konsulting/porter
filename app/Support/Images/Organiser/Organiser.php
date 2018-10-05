<?php

namespace App\Support\Images\Organiser;

use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository;
use App\Support\Images\Image;

class Organiser
{
    /** @var ImageRepository */
    protected $repository;

    /** @var Cli */
    protected $cli;

    public function __construct(ImageRepository $repository, Cli $cli)
    {
        $this->repository = $repository;
        $this->cli = $cli;
    }

    /**
     * Build the current images.
     *
     * @param string|null $service
     *
     * @throws \Exception
     */
    public function buildImages($service = null)
    {
        foreach ($this->repository->findByServiceName($service, $firstPartyOnly = true) as $image) {
            /* @var Image $image */
            $this->cli->passthru("docker build -t {$image->getName()} --rm {$image->getLocalPath()} --");
        }
    }

    /**
     * Push the current images.
     *
     * @param string|null $service
     *
     * @throws \Exception
     */
    public function pushImages($service = null)
    {
        foreach ($this->repository->findByServiceName($service, $firstPartyOnly = true) as $image) {
            /* @var Image $image */
            $this->cli->passthru("docker push {$image->getName()}");
        }
    }

    /**
     * Pull our docker images.
     *
     * @param string|null $service
     *
     * @throws \Exception
     */
    public function pullImages($service = null)
    {
        foreach ($this->repository->findByServiceName($service) as $image) {
            /** @var Image $image */
            if (running_tests() && $this->hasImage($image)) {
                continue;
            }

            $this->cli->passthru("docker pull {$image->getName()}");
        }
    }

    /**
     * Check if we already have the image.
     *
     * @param Image $image
     *
     * @return bool
     */
    public function hasImage(Image $image)
    {
        $output = $this->cli->exec("docker image inspect {$image->getName()}");

        return strpos($output, "Error: No such image: {$image->getName()}") === false;
    }
}
