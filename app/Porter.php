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
     * Start Porter containers
     */
    public function start()
    {
        DockerCompose::command('up -d')->realTime()->perform();
    }

    /**
     * Stop Porter containers
     */
    public function stop()
    {
        DockerCompose::command('down')->realTime()->perform();
    }

    /**
     * Restart Porter containers
     *
     * @param null $service
     */
    public function restart($service = null)
    {
        DockerCompose::command("restart {$service}")->realTime()->perform();
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
