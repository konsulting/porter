<?php

namespace App;

use App\DockerCompose\CliCommandFactory;
use App\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository;
use Symfony\Component\Finder\Finder;


class Porter
{
    /**
     * The docker images used by Porter to serve sites.
     *
     * @var ImageRepository
     */
    protected $images;

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

    public function __construct(ImageRepository $images, Cli $cli, CliCommandFactory $commandFactory)
    {
        $this->images = $images;
        $this->cli = $cli;
        $this->dockerCompose = $commandFactory;
    }

    /**
     * Check if the Porter containers are running
     *
     * @param null $service
     * @return bool
     */
    public function isUp($service = null)
    {
        return stristr($this->dockerCompose->command('ps')->perform(), "porter_{$service}");
    }

    /**
     * Create the docker-compose.yaml file
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function compose()
    {
        app(YamlBuilder::class)->build($this->getDockerImageSet());
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
        $images = collect($this->images->firstParty($this->getDockerImageSet()))->merge($this->images->thirdParty());

        foreach ($images as $image) {
            $this->cli->passthru("docker pull {$image}");
        }
    }

    /**
     * Build the current images
     */
    public function buildImages()
    {
        $imagesDir = base_path('docker/' . $this->getDockerImageSet());

        foreach (Finder::create()->in($imagesDir)->directories() as $dir) {
            /** @var $dir \SplFileInfo */
            $this->cli->passthru("docker build -t {$this->getDockerImageSet()}-{$dir->getFileName()}:latest --rm {$dir->getRealPath()} --");
        }
    }

    /**
     * Push the current images
     */
    public function pushImages()
    {
        foreach ($this->images->firstParty($this->getDockerImageSet()) as $image) {
            $this->cli->passthru("docker push {$image}");
        }
    }

    /**
     * Get the current image set to use.
     */
    public function getDockerImageSet()
    {
        return setting('docker_image_set', config('app.default-docker-image-set'));
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
     */
    public function logs()
    {
        echo $this->dockerCompose->command('logs')->perform();
    }
}
