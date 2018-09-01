<?php

namespace App\DockerCompose;

use App\PhpVersion;
use App\Support\Contracts\ImageRepository;

class YamlBuilder
{
    /**
     * Build the docker-compose.yaml file
     *
     * @param $imageSet
     * @throws \Throwable
     */
    public function build(ImageRepository $imageSet)
    {
        file_put_contents(
            config('porter.docker-compose-file'),
            view("docker_compose.{$imageSet->getName()}.base")->with([
                'home' => setting('home'),
                'host_machine_name' => setting('host_machine_name'),
                'activePhpVersions' => PhpVersion::active()->get(),
                'useMysql' => setting('use_mysql') == 'on',
                'useRedis' => setting('use_redis') == 'on',
                'useBrowser' => setting('use_browser') == 'on',
                'imageSet' => $imageSet->getName(),
                'imageSetPath' => $imageSet->getPath(),
                'libraryPath' => config('porter.library_path'),
            ])->render()
        );
    }

    /**
     * Destroy the docker-compose.yaml file
     */
    public function destroy()
    {
        @unlink(config('porter.docker-compose-file'));
    }
}
