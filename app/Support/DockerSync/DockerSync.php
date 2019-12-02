<?php

namespace App\Support\DockerSync;

use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Contracts\Cli;
use App\Support\Mechanics\MacOs;
use App\Support\Mechanics\Mechanic;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class DockerSync
{
    /** @var Mechanic */
    protected $mechanic;
    /** @var Cli */
    protected $cli;
    /** @var FileSystem */
    protected $files;
    /** @var PorterLibrary */
    private $library;

    public function __construct(Mechanic $mechanic, Cli $cli, Filesystem $files, PorterLibrary $library)
    {
        $this->mechanic = $mechanic;
        $this->cli = $cli;
        $this->files = $files;
        $this->library = $library;
    }

    /**
     * Check if docker-sync is active
     * @return bool
     */
    public function isActive()
    {
        return setting('use_docker-sync') === 'on';
    }

    /**
     * Install docker-sync
     *
     * @throws CannotInstallDockerSync
     */
    public function install()
    {
        $this->checkForMacOs();

        $this->cli->passthru('gem install --user-install docker-sync');
    }

    /**
     * Check this command is running on MacOS
     * @throws CannotInstallDockerSync
     */
    protected function checkForMacOs(): void
    {
        if (get_class($this->mechanic) !== MacOs::class) {
            throw new CannotInstallDockerSync('The OS must be MacOs');
        }
    }

    /**
     * Adjust the docker-compose file to point to sync volumes, create docker-sync.yaml
     *
     * MySQL and Redis usage not explored as yet.
     *
     * @param  string  $composeFile
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function adjustDockerComposeSetup(string $composeFile)
    {
        if (!$this->isActive()) {
            return;
        }

        $composeYaml = $this->getYaml($composeFile);
        $syncYamlFile = dirname($composeFile).'/docker-sync.yml';
        $syncYaml = [
            'version' => 2,
            'syncs'   => $this->getSyncs(),
        ];

        foreach (PhpVersion::active()->get() as $version) {
            $composeYaml['services'][$version->cli_name]['volumes'][0] = $this->replaceSync($composeYaml['services'][$version->cli_name]['volumes'][0]);
            $composeYaml['services'][$version->fpm_name]['volumes'][0] = $this->replaceSync($composeYaml['services'][$version->fpm_name]['volumes'][0]);
        }

        $composeYaml['services']['node']['volumes'][0] = $this->replaceSync($composeYaml['services']['node']['volumes'][0]);
        $composeYaml['services']['nginx']['volumes'][0] = $this->replaceSync($composeYaml['services']['nginx']['volumes'][0]);

        $composeYaml['volumes'] = array_map(function () {
            return ['external' => true];
        }, $this->getSyncs());

        $this->putYaml($composeFile, $composeYaml);
        $this->putYaml($syncYamlFile, $syncYaml);
    }

    protected function getSyncs()
    {
        return [
            'home' => [
                'src'           => setting('home'),
                'watch_excludes'=> ['.*/.git', '.*/node_modules'],
            ],
        ];
    }

    protected function replaceSync($string, $sync = 'home')
    {
        $pathParts = explode(':', $string);

        $source = $pathParts[0];
        $target = $pathParts[1];

        $syncPath = $this->getSyncs()[$sync]['src'];

        if ($source !== $syncPath) {
            return $string;
        }

        return implode(':', [$sync, $target, 'nocopy']);
    }

    /**
     * Get the yaml from the file
     *
     * @param  string  $file
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getYaml(string $file)
    {
        return Yaml::parse($this->files->get($file));
    }

    /**
     * Save array to yaml file
     *
     * @param  string  $file
     * @param  array   $yaml
     */
    public function putYaml(string $file, array $yaml)
    {
        $this->files->put($file, Yaml::dump($yaml, 5, 2));
    }

    /**
     * Start the docker-syn daemon
     */
    public function startDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->execRealTime($this->getPath().'docker-sync start --config="'.$this->library->path().'/docker-sync.yml"');
    }

    /**
     * Stop the docker-sync daemon
     */
    public function stopDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->execRealTime($this->getPath().'docker-sync stop --config="'.$this->library->path().'/docker-sync.yml"');
    }

    /**
     * Get the path for docker-sync
     *
     * @return string
     */
    public function getPath()
    {
        return str_replace("\n", '', $this->cli->exec("ruby -r rubygems -e 'puts Gem.user_dir'")).'/bin/';
    }
}
