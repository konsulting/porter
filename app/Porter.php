<?php
namespace App;

use App\DockerCompose\YamlBuilder;

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
}
