<?php
namespace App;

use App\DockerCompose\YamlBuilder;

class Porter
{
    public function isUp()
    {
        $output = [];
        exec(docker_compose("ps | grep porter_"), $output);

        return ! empty($output);
    }

    public function compose()
    {
        app(YamlBuilder::class)->build();
    }

    public function start()
    {
        exec(docker_compose("up -d"));
    }

    public function stop()
    {
        exec(docker_compose("down"));
    }

    public function build()
    {
        exec(docker_compose("build"));
    }
}
