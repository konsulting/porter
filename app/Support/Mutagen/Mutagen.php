<?php

namespace App\Support\Mutagen;

use App\Models\PhpVersion;
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

    /**
     * Check if mutagen is already active.
     *
     * @return bool
     */
    public function isActive()
    {
        return setting('use_mutagen') === 'on';
    }

    /**
     * Install mutagen.
     *
     * @throws CannotInstallMutagen
     */
    public function install()
    {
        $this->checkForMacOs();

        $this->cli->passthru('brew install havoc-io/mutagen/mutagen');
    }

    /**
     * Start the mutagen daemon.
     */
    public function startDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->exec('mutagen daemon start');
    }

    /**
     * Stop the mutagen daemon and terminate sync processes.
     */
    public function stopDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->exec('mutagen sync terminate -a');
        $this->cli->exec('mutagen daemon stop');
    }

    /**
     * Add syncs for php fpm and nginx volume. Mutagen is not recommended for use with
     * mysql/redis volumes, and cannot be used with the PHP CLI/Node containers since
     * they do not remain started.
     */
    public function syncVolumes()
    {
        if (!$this->isActive()) {
            return;
        }

        $home = setting('home');

        foreach (PhpVersion::active()->get() as $version) {
            $this->syncVolume($home, $version->fpm_name, '/srv/app');
        }

        $this->syncVolume($home, 'nginx', '/srv/app');
    }

    /**
     * Spawn a sync process for a volume.
     *
     * @param $localPath
     * @param $container
     * @param $containerPath
     */
    public function syncVolume($localPath, $container, $containerPath)
    {
        if (!$this->isActive()) {
            return;
        }

        $containerPath = Str::start($containerPath, '/');
        $this->cli->passthru("mutagen sync create --symlink-mode=ignore --ignore-vcs --ignore=['node_modules'] {$localPath} \docker://porter_{$container}_1{$containerPath}");
    }

    /**
     * Check that the command is running on MacOS.
     *
     * @throws CannotInstallMutagen
     */
    protected function checkForMacOs(): void
    {
        if (get_class($this->mechanic) !== MacOs::class) {
            throw new CannotInstallMutagen('The OS must be MacOs');
        }
    }

    /**
     * Remove the synced volumes from docker-compose.yaml.
     *
     * @param string $file
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function removeVolumesFromDockerCompose(string $file)
    {
        if (!$this->isActive()) {
            return;
        }

        $yaml = $this->getYaml($file);

        foreach (PhpVersion::active()->get() as $version) {
            unset($yaml['services'][$version->fpm_name]['volumes'][0]);
            $yaml['services'][$version->fpm_name]['volumes'] = array_values($yaml['services'][$version->fpm_name]['volumes']);
        }

        unset($yaml['services']['nginx']['volumes'][0]);
        $yaml['services']['nginx']['volumes'] = array_values($yaml['services']['nginx']['volumes']);

        $this->putYaml($file, $yaml);
    }

    /**
     * Get the yaml from the file.
     *
     * @param string $file
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return mixed
     */
    public function getYaml(string $file)
    {
        return Yaml::parse($this->files->get($file));
    }

    /**
     * Save array to yaml file.
     *
     * @param string $file
     * @param array  $yaml
     */
    public function putYaml(string $file, array $yaml)
    {
        $this->files->put($file, Yaml::dump($yaml, 5, 2));
    }
}
