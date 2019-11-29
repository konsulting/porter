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
    /** @var string|null */
    protected $homePath;
    /** @var PorterLibrary */
    private $library;

    public function __construct(Mechanic $mechanic, Cli $cli, Filesystem $files, PorterLibrary $library)
    {
        $this->mechanic = $mechanic;
        $this->cli = $cli;
        $this->files = $files;
        $this->homePath = setting('home');
        $this->library = $library;
    }

    public function isActive()
    {
        return setting('use_docker-sync') === 'on';
    }

    public function install()
    {
        $this->checkForMacOs();

        $this->cli->passthru('gem install --user-install docker-sync');
    }

    protected function checkForMacOs(): void
    {
        if (get_class($this->mechanic) !== MacOs::class) {
            throw new CannotInstallDockerSync('The OS must be MacOs');
        }
    }

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

//        if (setting('use_mysql') === 'on') {
//            $composeYaml['services']['mysql']['volumes'][0] = $this->replaceSync($composeYaml['services']['mysql']['volumes'][0], 'mysql-data');
//        }
//
//        if (setting('use_redis') === 'on') {
//            $composeYaml['services']['redis']['volumes'][0] = $this->replaceSync($composeYaml['services']['redis']['volumes'][0], 'redis-data');
//        }

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
//        $this->putYaml(str_replace('docker-compose', 'docker-compose-dev', $composeFile), $composeYaml);
        $this->putYaml($syncYamlFile, $syncYaml);
    }

    protected function getSyncs()
    {
        return [
            'home' => [
                'src'           => $this->homePath,
                'watch_excludes'=> ['.*/.git', '.*/node_modules'],
            ],
//            'mysql-data' => $this->library->path().'/data/mysql',
//            'redis-data' => $this->library->path().'/data/redis',
        ];
    }

    protected function replaceSync($string, $sync = 'home')
    {
        $pathParts = explode(':', $string);

        $source = $pathParts[0];
        $target = $pathParts[1];
        // $settings = $pathParts[2] ?? '';

        $syncPath = $this->getSyncs()[$sync]['src'];

        if ($source !== $syncPath) {
            return $string;
        }

        return implode(':', [$sync, $target, 'nocopy']);
//        return [
//            'type' => 'volume',
//            'source' => $sync,
//            'target' => $target,
//            'volume' => ['nocopy' => true],
//        ];
    }

    public function getYaml(string $file)
    {
        return Yaml::parseFile($file);
    }

    public function putYaml(string $file, array $yaml)
    {
        $this->files->put($file, Yaml::dump($yaml, 5, 2));
    }

    public function startDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->execRealTime($this->getPath().'docker-sync start --config="'.$this->library->path().'/docker-sync.yml"');
    }

    public function stopDaemon()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->cli->execRealTime($this->getPath().'docker-sync stop --config="'.$this->library->path().'/docker-sync.yml"');
    }

    public function getPath()
    {
        return str_replace("\n", '', $this->cli->exec("ruby -r rubygems -e 'puts Gem.user_dir'")).'/bin/';
    }
}
