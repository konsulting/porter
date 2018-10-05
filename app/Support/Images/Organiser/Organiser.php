<?php

namespace App\Support\Images\Organiser;

use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository;
use App\Support\Images\Image;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class Organiser
{
    /** @var ImageRepository */
    protected $repository;

    /** @var Cli */
    protected $cli;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct(ImageRepository $repository, Cli $cli, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->cli = $cli;
        $this->filesystem = $filesystem;
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
            /** @var Image $image */
            $version = $this->findBuildVersion($image);
            $name = $image->getUnVersionedName();

            $this->cli->passthru("docker build -t {$name}:{$version} -t {$name}:latest --rm {$image->getLocalPath()} --");

            $this->updateConfigVersionForImage($image, $version);
        }
    }

    /**
     * Find the build version for an image.
     * Look in the first line of the Dockerfile.
     *
     * @param Image $image
     *
     * @throws FileNotFoundException
     *
     * @return string
     */
    public function findBuildVersion(Image $image)
    {
        $dockerFile = $this->filesystem->get($image->getLocalPath().'/Dockerfile');

        return (string) $this->readVersion(strtok($dockerFile, "\n"));
    }

    /**
     * Read the version string, validate and return version.
     *
     * @param string $versionString
     *
     * @throws \Exception
     *
     * @return string
     */
    public function readVersion($versionString)
    {
        if (!preg_match('/#VERSION:\s{0,}\d+\.\d+\.\d+/', $versionString)) {
            throw new \Exception(
                "The version must be the first line of the Dockerfile and in the format '#VERSION: x.y.z'"
            );
        }

        return trim(str_replace('#VERSION:', '', $versionString));
    }

    /**
     * Update the version stored in the config.json file.
     *
     * @param Image $image
     * @param $version
     *
     * @throws FileNotFoundException
     */
    public function updateConfigVersionForImage(Image $image, $version)
    {
        $configFile = $this->repository->getPath().'/config.json';
        $config = json_decode($this->filesystem->get($configFile), true);

        $serviceName = substr($image->getUnVersionedName(), strlen($config['name']) + 1);

        $config['firstParty'][$serviceName] = $version;

        $this->filesystem->put($configFile, json_encode($config, JSON_PRETTY_PRINT));
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
