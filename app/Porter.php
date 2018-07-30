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
        exec(docker_compose("ps | grep porter_{$service}"), $output);

        return ! empty($output);
    }

    /**
     * Create the docker-compose.yaml file
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function compose()
    {
        app(YamlBuilder::class)->build();
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
     * Our images in the Konsulting repo
     *
     * @return array
     */
    protected function ourImages()
    {
        $images = [
            'konsulting/porter-nginx:latest',
            'konsulting/porter-node:latest',
        ];

        foreach (PhpVersion::all() as $version) {
            $images[] = 'konsulting/porter-php_cli_'.$version->safe.':latest';
            $images[] = 'konsulting/porter-php_fpm_'.$version->safe.':latest';
        }

        return $images;
    }

    /**
     * Third Party images
     *
     * @return array
     */
    protected function thirdPartyImages()
    {
        return [
            'mysql:5.7',
            'redis:alpine',
        ];
    }

    /**
     * Build the Konsulting images
     */
    public function buildImages()
    {
        foreach (Finder::create()->in(base_path('docker'))->directories() as $dir) {
            passthru("docker build -t konsulting/porter-{$dir->getFileName()}:latest --rm {$dir->getRealPath()} --");
        }
    }

    /**
     * Push the Konsulting images
     */
    public function pushImages()
    {
        foreach ($this->ourImages() as $image) {
            passthru("docker push {$image}");
        }
    }
}
