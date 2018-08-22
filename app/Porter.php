<?php
namespace App;

use App\DockerCompose\CliCommand as DockerCompose;
use App\DockerCompose\YamlBuilder;
use App\Support\Cli;
use Symfony\Component\Finder\Finder;

class Porter
{
    /**
     * Check if the Porter containers are running
     *
     * @param null $service
     * @return bool
     */
    public function isUp($service = null)
    {
        return stristr(DockerCompose::command('ps')->perform(), "porter_{$service}");
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

        DockerCompose::command("up -d {$recreate}--remove-orphans {$service}")->realTime()->perform();
    }

    /**
     * Stop Porter containers
     * @param null $service
     */
    public function stop($service = null)
    {
        if ($service) {
            DockerCompose::command("stop {$service}")->realTime()->perform();

            return;
        }

        DockerCompose::command("down --remove-orphans")->realTime()->perform();
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
        app(Porter::class)->compose();

        PhpVersion::active()
            ->get()
            ->reject(function ($phpVersion) {
                return $this->isUp($phpVersion->fpm_name);
            })
            ->each(function ($phpVersion) {
                $this->start($phpVersion->fpm_name);
                $this->start($phpVersion->cli_name);
            });

        app(Porter::class)->restart('nginx');
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

        app(Porter::class)->compose();
        app(Porter::class)->start($service);
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

        app(Porter::class)->stop($service);
        app(Porter::class)->compose();
    }

    /**
     * (Re)build Porter containers
     */
    public function build()
    {
        DockerCompose::command('build')->perform();
    }

    /**
     * Pull our docker images
     */
    public function pullImages()
    {
        $images = collect($this->ourImages())->merge($this->thirdPartyImages());

        foreach ($images as $image) {
            $this->getCli()->passthru("docker pull {$image}");
        }
    }

    /**
     * Current images that are being pulled when we install, as they're used
     * when starting porter, rather than waiting for them at that point.
     *
     * @return array
     */
    protected function ourImages()
    {
        $images = [];
        $imagesDir = base_path('docker/'.$this->getDockerImageSet());

        foreach (Finder::create()->in($imagesDir)->directories() as $dir) {
            $images[] = $this->getDockerImageSet().'-'.$dir->getFileName().':latest';
        }

        return $images;
    }

    /**
     * Third Party images. A list of images to pull when we install, as they're
     * used when starting porter, rather than waiting for them at that point.
     *
     * @return array
     */
    protected function thirdPartyImages()
    {
        return [
            'mysql:5.7',
            'redis:alpine',
            'andyshinn/dnsmasq',
            'mailhog/mailhog:v1.0.0',
        ];
    }

    /**
     * Build the current images
     */
    public function buildImages()
    {
        $imagesDir = base_path('docker/'.$this->getDockerImageSet());

        foreach (Finder::create()->in($imagesDir)->directories() as $dir) {
            /** @var $dir \SplFileInfo */
            $this->getCli()->passthru("docker build -t {$this->getDockerImageSet()}-{$dir->getFileName()}:latest --rm {$dir->getRealPath()} --");
        }
    }

    /**
     * Push the current images
     */
    public function pushImages()
    {
        foreach ($this->ourImages() as $image) {
            $this->getCli()->passthru("docker push {$image}");
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
        echo DockerCompose::command('ps')->perform();
    }

    /**
     * Show container logs
     */
    public function logs()
    {
        echo DockerCompose::command('logs')->perform();
    }

    /**
     * Get the Cli class
     *
     * @return Cli
     */
    protected function getCli()
    {
        return app(Cli::class);
    }
}
