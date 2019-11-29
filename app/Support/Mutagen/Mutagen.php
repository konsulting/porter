<?php

namespace App\Support\Mutagen;

use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Contracts\Cli;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Mechanic;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class Mutagen
{
    /** @var Mechanic */
    protected $mechanic;
    /** @var Cli */
    protected $cli;
    /** @var FileSystem */
    protected $files;

    public function __construct(Mechanic $mechanic, Cli $cli, Filesystem $files)
    {
        $this->mechanic = $mechanic;
        $this->cli = $cli;
        $this->files = $files;
    }

    // Mutagen needs to hook into porter start/stop/restart to start/stop itself
    // Mutagen needs to stop some volumes form being linked in Docker

    public function isActive()
    {
        return setting('use_mutagen') === 'on';
    }

    public function install()
    {
        $this->checkForMacOs();

        $this->cli->passthru('brew install havoc-io/mutagen/mutagen');
    }

    public function startDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->exec('mutagen daemon start');
    }

    public function stopDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->exec('mutagen sync terminate -a');
        $this->cli->exec('mutagen daemon stop');
    }

    public function syncVolumes()
    {
        if (!$this->isActive()) {
            return;
        }

        /** @var PorterLibrary $lib */
        $lib = app(PorterLibrary::class);

//        if (setting('use_mysql') === 'on') {
//            $this->syncVolume($lib->path().'/data/mysql', 'mysql', '/var/lib/mysql');
//        }

//        if (setting('use_redis') === 'on') {
//            $this->syncVolume($lib->path().'/data/redis', 'redis', '/data');
//        }

        $home = setting('home');

        foreach (PhpVersion::active()->get() as $version) {
//            $this->syncVolume($home, $version->cli_name, '/srv/app');
            $this->syncVolume($home, $version->fpm_name, '/srv/app');
        }

        $this->syncVolume($home, 'nginx', '/srv/app');
//        $this->syncVolume($home, 'node', '/srv/app');
    }

    public function syncVolume($localPath, $container, $containerPath)
    {
        if (!$this->isActive()) {
            return;
        }

        $containerPath = Str::start($containerPath, '/');
        $this->cli->passthru("mutagen sync create --symlink-mode=ignore --ignore-vcs --ignore=['node_modules'] {$localPath} \docker://porter_{$container}_1{$containerPath}");
    }

    protected function checkForMacOs(): void
    {
        if (get_class($this->mechanic) !== MacOs::class) {
            throw new CannotInstallDockerSync('The OS must be MacOs');
        }
    }

    public function removeVolumesFromDockerCompose(string $file)
    {
        if (!$this->isActive()) {
            return;
        }

        $yaml = $this->getYaml($file);

//        if (setting('use_mysql') === 'on') {
//            unset($yaml['services']['mysql']['volumes'][0]);
//            $yaml['services']['mysql']['volumes'] = array_values($yaml['services']['mysql']['volumes']);
//        }

//        if (setting('use_redis') === 'on') {
//            unset($yaml['services']['redis']['volumes']);
//        }

        foreach (PhpVersion::active()->get() as $version) {
//            unset($yaml['services'][$version->cli_name]['volumes'][0]);
            unset($yaml['services'][$version->fpm_name]['volumes'][0]);
//            $yaml['services'][$version->cli_name]['volumes'] = array_values($yaml['services'][$version->cli_name]['volumes']);
            $yaml['services'][$version->fpm_name]['volumes'] = array_values($yaml['services'][$version->fpm_name]['volumes']);
        }

//        unset($yaml['services']['node']['volumes'][0]);
        unset($yaml['services']['nginx']['volumes'][0]);
//        $yaml['services']['node']['volumes'] = array_values($yaml['services']['node']['volumes']);
        $yaml['services']['nginx']['volumes'] = array_values($yaml['services']['nginx']['volumes']);

        $this->putYaml($file, $yaml);
    }

    public function getYaml(string $file)
    {
        return Yaml::parseFile($file);
    }

    public function putYaml(string $file, array $yaml)
    {
        $this->files->put($file, Yaml::dump($yaml, 5, 2));
    }
}
