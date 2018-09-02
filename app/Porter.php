<?php

namespace App;

use App\DockerCompose\CliCommandFactory;
use App\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository;
use App\Support\Contracts\ImageSetRepository;

class Porter
{
    /**
     * The docker images sets used by Porter to serve sites.
     *
     * @var ImageSetRepository
     */
    protected $imageSets;

    /**
     * The CLI class that executes commands.
     *
     * @var Cli
     */
    protected $cli;

    /**
     * The Docker composer command factory.
     *
     * @var CliCommandFactory
     */
    protected $dockerCompose;

    /**
     * The DockerCompose YAML file builder
     *
     * @var YamlBuilder
     */
    protected $yamlBuilder;

    /**
     * Porter constructor.
     *
     * @param ImageSetRepository $imageSets
     * @param Cli $cli
     * @param CliCommandFactory $commandFactory
     * @param YamlBuilder $yamlBuilder
     */
    public function __construct(
        ImageSetRepository $imageSets,
        Cli $cli,
        CliCommandFactory $commandFactory,
        YamlBuilder $yamlBuilder
    ){
        $this->imageSets = $imageSets;
        $this->cli = $cli;
        $this->dockerCompose = $commandFactory;
        $this->yamlBuilder = $yamlBuilder;
    }

    /**
     * Check if the Porter containers are running
     *
     * @param null $service
     * @return bool
     */
    public function isUp($service = null)
    {
        return (bool) stristr($this->dockerCompose->command('ps')->perform(), "porter_{$service}");
    }

    /**
     * Create the docker-compose.yaml file
     */
    public function compose()
    {
        $this->yamlBuilder->build($this->getDockerImageSet());
    }

    /**
     * Start Porter containers, optionally start a specific service, and force them to be recreated
     *
     * @param null $service
     * @param bool $recreate
     */
    public function start($service = null, $recreate = false)
    {
        $recreate = $recreate ? '--force-recreate ' : '';

        $this->dockerCompose->command("up -d {$recreate}--remove-orphans {$service}")->realTime()->perform();
    }

    /**
     * Stop Porter containers
     *
     * @param null $service
     */
    public function stop($service = null)
    {
        if ($service) {
            $this->dockerCompose->command("stop {$service}")->realTime()->perform();

            return;
        }

        $this->dockerCompose->command("down --remove-orphans")->realTime()->perform();
    }

    /**
     * Restart Porter containers
     *
     * @param null $service
     */
    public function restart($service = null)
    {
        if ($this->isUp($service)) {
            $this->stop($service);
        }

        // If we're restarting something it's probably because config changed - so force recreation
        $this->start($service, true);
    }

    /**
     * Restart serving, picking up changes in used PHP versions and NGiNX
     */
    public function restartServing()
    {
        // Build up docker-compose again - so we pick up any new PHP containers to be used
        $this->compose();

        if (! $this->isUp()) {
            return;
        }

        PhpVersion::active()
            ->get()
            ->reject(function ($phpVersion) {
                return $this->isUp($phpVersion->fpm_name);
            })
            ->each(function ($phpVersion) {
                $this->start($phpVersion->fpm_name);
                $this->start($phpVersion->cli_name);
            });

        $this->restart('nginx');
    }

    /**
     * Turn a service on
     *
     * @param $service
     */
    public function turnOnService($service)
    {
        if (setting("use_{$service}") == 'on') {
            return;
        }

        Setting::updateOrCreate("use_{$service}", 'on');

        $this->compose();

        if ($this->isUp()) {
            $this->start($service);
        }
    }

    /**
     * Turn a service off
     *
     * @param $service
     */
    public function turnOffService($service)
    {
        if (setting("use_{$service}") == 'off') {
            return;
        }

        Setting::updateOrCreate("use_{$service}", 'off');

        if ($this->isUp()) {
            $this->stop($service);
        }
        $this->compose();
    }

    /**
     * (Re)build Porter containers
     */
    public function build()
    {
        $this->dockerCompose->command('build')->perform();
    }

    /**
     * Pull our docker images
     */
    public function pullImages()
    {
        foreach ($this->getDockerImageSet()->all() as $image) {
            $this->cli->passthru("docker pull {$image->name}");
        }
    }

    /**
     * Build the current images
     */
    public function buildImages()
    {
        foreach ($this->getDockerImageSet()->firstParty() as $image) {
            $this->cli->passthru("docker build -t {$image->name} --rm {$image->localPath} --");
        }
    }

    /**
     * Push the current images
     */
    public function pushImages()
    {
        foreach ($this->getDockerImageSet()->firstParty() as $image) {
            $this->cli->passthru("docker push {$image->name}");
        }
    }

    /**
     * Get the current image set to use.
     *
     * @return ImageRepository
     */
    public function getDockerImageSet()
    {
        return $this->imageSets->getImageRepository(
            setting('docker_image_set', config('porter.default-docker-image-set'))
        );
    }

    /**
     * Show container status
     */
    public function status()
    {
        echo $this->dockerCompose->command('ps')->perform();
    }

    /**
     * Show container logs
     *
     * @param null $service
     */
    public function logs($service = null)
    {
        echo $this->dockerCompose->command("logs {$service}")->perform();
    }
}
