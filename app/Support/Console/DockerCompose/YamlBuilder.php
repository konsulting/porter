<?php

namespace App\Support\Console\DockerCompose;

use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Contracts\ImageRepository;
use Illuminate\Filesystem\Filesystem;

class YamlBuilder
{
    /** @var Filesystem */
    protected $files;

    /** @var PorterLibrary */
    private $porterLibrary;

    public function __construct(Filesystem $files, PorterLibrary $porterLibrary)
    {
        $this->files = $files;
        $this->porterLibrary = $porterLibrary;
    }

    /**
     * Build the docker-compose.yaml file.
     *
     * @param $imageSet
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function build(ImageRepository $imageSet)
    {
        $path = $this->porterLibrary->dockerComposeFile();

        $this->files->put(
            $path,
            $this->renderDockerComposeFile($imageSet)
        );

        return $path;
    }

    /**
     * Render the docker compose file.
     *
     * @param ImageRepository $imageSet
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function renderDockerComposeFile(ImageRepository $imageSet)
    {
        return (string) view("{$imageSet->getName()}::base")->with([
            'home'              => setting('home'),
            'host_machine_name' => setting('host_machine_name'),
            'activePhpVersions' => PhpVersion::active()->get(),
            'useMysql'          => setting('use_mysql') === 'on',
            'useRedis'          => setting('use_redis') === 'on',
            'useBrowser'        => setting('use_browser') === 'on',
            'useDns'            => setting('use_dns') === 'on' || setting_missing('use_dns'),
            'httpPort'          => setting('http_port', 80),
            'httpsPort'         => setting('https_port', 443),
            'imageSet'          => $imageSet,
            'libraryPath'       => $this->porterLibrary->path(),
        ])->render();
    }

    /**
     * Destroy the docker-compose.yaml file.
     */
    public function destroy()
    {
        $this->files->delete($this->porterLibrary->dockerComposeFile());
    }
}
