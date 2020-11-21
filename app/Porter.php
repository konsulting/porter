<?php

namespace App;

use App\Events\BuiltDockerCompose;
use App\Events\StartedPorter;
use App\Events\StartedPorterService;
use App\Events\StartingPorter;
use App\Events\StartingPorterService;
use App\Events\StoppedPorter;
use App\Events\StoppedPorterService;
use App\Events\StoppingPorter;
use App\Events\StoppingPorterService;
use App\Models\PhpVersion;
use App\Models\Setting;
use App\Support\Console\DockerCompose\CliCommandFactory;
use App\Support\Console\DockerCompose\YamlBuilder;
use App\Support\Contracts\Cli;
use App\Support\Contracts\ImageRepository;
use App\Support\Contracts\ImageSetRepository;

class Porter
{
    /**
     * The docker images sets used by Porter to serve sites.
     *
     * @var ImageSetRepository
     */
    protected $imageSets;

    /**
     * The CLI class that executes commands.
     *
     * @var Cli
     */
    protected $cli;

    /**
     * The Docker composer command factory.
     *
     * @var CliCommandFactory
     */
    protected $dockerCompose;

    /**
     * The DockerCompose YAML file builder.
     *
     * @var YamlBuilder
     */
    protected $yamlBuilder;

    /**
     * Porter constructor.
     *
     * @param ImageSetRepository $imageSets
     * @param Cli                $cli
     * @param CliCommandFactory  $commandFactory
     * @param YamlBuilder        $yamlBuilder
     */
    public function __construct(
        ImageSetRepository $imageSets,
        Cli $cli,
        CliCommandFactory $commandFactory,
        YamlBuilder $yamlBuilder
    ) {
        $this->imageSets = $imageSets;
        $this->cli = $cli;
        $this->dockerCompose = $commandFactory;
        $this->yamlBuilder = $yamlBuilder;
    }

    /**
     * Check if the Porter containers are running.
     *
     * @param string|null $service
     *
     * @return bool
     */
    public function isUp($service = null)
    {
        return (bool) stristr($this->dockerCompose->command('ps')->perform(), "porter_{$service}");
    }

    /**
     * Create the docker-compose.yaml file.
     */
    public function compose()
    {
        $filePath = $this->yamlBuilder->build($this->getDockerImageSet());

        event(new BuiltDockerCompose($filePath));
    }

    /**
     * Start Porter containers, optionally start a specific service, and force them to be recreated.
     *
     * @param string|null $service
     * @param bool        $recreate
     */
    public function start($service = null, $recreate = false)
    {
        $recreate = $recreate ? '--force-recreate ' : '';

        event(is_null($service) ? new StartingPorter() : new StartingPorterService($service));

        $this->dockerCompose->command("up -d {$recreate}--remove-orphans {$service}")->realTime()->perform();

        event(is_null($service) ? new StartedPorter() : new StartedPorterService($service));
    }

    /**
     * Stop Porter containers.
     *
     * @param string|null $service
     */
    public function stop($service = null)
    {
        event(is_null($service) ? new StoppingPorter() : new StoppingPorterService($service));

        if ($service) {
            $this->dockerCompose->command("stop {$service}")->realTime()->perform();
            event(new StoppedPorterService($service));

            return;
        }

        $this->dockerCompose->command('down --remove-orphans')->realTime()->perform();
        event(new StoppedPorter());
    }

    /**
     * Restart Porter containers.
     *
     * @param string|null $service
     */
    public function restart($service = null)
    {
        if ($this->isUp($service)) {
            $this->stop($service);
        }

        // If we're restarting something it's probably because config changed - so force recreation
        $this->start($service, true);
    }

    /**
     * Soft restart Porter containers.
     * Restart without getting config changes.
     *
     * @param string|null $service
     */
    public function softRestart($service = null)
    {
        $this->dockerCompose->command("restart {$service}")->realTime()->perform();
    }

    /**
     * Restart serving, picking up changes in used PHP versions and NGiNX.
     */
    public function restartServing()
    {
        // Build up docker-compose again - so we pick up any new PHP containers to be used
        $this->compose();

        if (!$this->isUp()) {
            return;
        }

        PhpVersion::active()
            ->get()
            ->reject(function ($phpVersion) {
                return $this->isUp($phpVersion->fpm_name);
            })
            ->each(function ($phpVersion) {
                $this->start($phpVersion->fpm_name);
                $this->start($phpVersion->cli_name);
            });

        $this->restart('nginx');
    }

    /**
     * Turn a service on.
     *
     * @param string $service
     */
    public function turnOnService($service)
    {
        if (setting("use_{$service}") == 'on') {
            return;
        }

        Setting::updateOrCreate("use_{$service}", 'on');

        $this->compose();

        if ($this->isUp()) {
            $this->start($service);
        }
    }

    /**
     * Turn a service off.
     *
     * @param string $service
     */
    public function turnOffService($service)
    {
        if (in_array(setting("use_{$service}"), [null, 'off'])) {
            return;
        }

        Setting::updateOrCreate("use_{$service}", 'off');

        if ($this->isUp()) {
            $this->stop($service);
        }
        $this->compose();
    }

    /**
     * (Re)build Porter containers.
     */
    public function build()
    {
        $this->dockerCompose->command('build')->perform();
    }

    /**
     * Get the current image set to use.
     *
     * @return ImageRepository
     */
    public function getDockerImageSet()
    {
        return $this->imageSets->getImageRepository(
            setting('docker_image_set', config('porter.default-docker-image-set'))
        );
    }

    /**
     * Show container status.
     */
    public function status()
    {
        echo $this->dockerCompose->command('ps')->perform();
    }

    /**
     * Show container logs.
     *
     * @param string|null $service
     */
    public function logs($service = null)
    {
        echo $this->dockerCompose->command("logs {$service}")->perform();
    }
}
