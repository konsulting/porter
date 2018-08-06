<?php
namespace App;

use App\DockerCompose\YamlBuilder;
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
        $output = [];

        exec(docker_compose("ps"), $output);

        return stristr(implode($output), "porter_{$service}");
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
        exec(docker_compose("up -d"));
    }

    /**
     * Stop Porter containers
     */
    public function stop()
    {
        exec(docker_compose("down"));
    }

    /**
     * Restart Porter containers
     *
     * @param null $service
     */
    public function restart($service = null)
    {
        exec(docker_compose(trim("restart {$service}")));
    }

    /**
     * (Re)build Porter containers
     */
    public function build()
    {
        exec(docker_compose("build"));
    }

    /**
     * Pull our docker images
     */
    public function pullImages()
    {
        $images = collect($this->ourImages())->merge($this->thirdPartyImages());

        foreach ($images as $image) {
            passthru("docker pull {$image}", $output);
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
            passthru("docker build -t {$this->getDockerImageSet()}-{$dir->getFileName()}:latest --rm {$dir->getRealPath()} --");
        }
    }

    /**
     * Push the current images
     */
    public function pushImages()
    {
        foreach ($this->ourImages() as $image) {
            passthru("docker push {$image}");
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
        passthru(docker_compose("ps"));
    }

    /**
     * Show container logs
     */
    public function logs()
    {
        passthru(docker_compose("logs"));
    }
}
