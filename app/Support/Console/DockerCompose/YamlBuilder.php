<?php

namespace App\Support\Console\DockerCompose;

use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Contracts\ImageRepository;
use Illuminate\Filesystem\Filesystem;

class YamlBuilder
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Build the docker-compose.yaml file.
     *
     * @param $imageSet
     *
     * @throws \Throwable
     */
    public function build(ImageRepository $imageSet)
    {
        $lib = app(PorterLibrary::class);

        $this->files->put(
            $lib->dockerComposeFile(),
            view("docker_compose.{$imageSet->getName()}.base")->with([
                'home'              => setting('home'),
                'host_machine_name' => setting('host_machine_name'),
                'activePhpVersions' => PhpVersion::active()->get(),
                'useMysql'          => setting('use_mysql') == 'on',
                'useRedis'          => setting('use_redis') == 'on',
                'useBrowser'        => setting('use_browser') == 'on',
                'imageSet'          => $imageSet->getName(),
                'imageSetPath'      => $imageSet->getPath(),
                'libraryPath'       => $lib->path(),
            ])->render()
        );
    }

    /**
     * Destroy the docker-compose.yaml file.
     */
    public function destroy()
    {
        $this->files->delete(app(PorterLibrary::class)->dockerComposeFile());
    }
}
