<?php

namespace App\DockerCompose;

use App\PhpVersion;

class YamlBuilder
{
    /**
     * Build the docker-compose.yaml file
     *
     * @param $imageSet
     * @throws \Throwable
     */
    public function build($imageSet)
    {
        file_put_contents(
            config('app.docker-compose-file'),
            view("docker_compose.{$imageSet}.base")->with([
                'home' => setting('home'),
                'host_machine_name' => setting('host_machine_name'),
                'activePhpVersions' => PhpVersion::active()->get(),
                'useMysql' => setting('use_mysql') == 'on',
                'useRedis' => setting('use_redis') == 'on',
                'imageSet' => $imageSet,
            ])->render()
        );
    }

    /**
     * Destroy the docker-compose.yaml file
     */
    public function destroy()
    {
        @unlink(config('app.docker-compose-file'));
    }
}
