<?php

namespace App;

use App\Exceptions\PorterSetupFailed;
use App\Support\FilePublisher;
use App\Support\Mechanics\Mechanic;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class PorterLibrary
{
    /**
     * The path of the Porter library directory (e.g. ~/.porter on Mac).
     *
     * @var string
     */
    protected $path;

    /**
     * The system's filesystem.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The file publisher.
     *
     * @var \App\Support\FilePublisher
     */
    protected $filePublisher;

    protected $shouldMigrateAndSeedDatabase = true;
    /**
     * @var Mechanic
     */
    private $mechanic;

    public function __construct(FilePublisher $filePublisher, Mechanic $mechanic, $path)
    {
        $this->filePublisher = $filePublisher;
        $this->files = $filePublisher->getFilesystem();
        $this->path = $path;
        $this->mechanic = $mechanic;
    }

    /**
     * Set the Mechanic instance.
     *
     * @param Mechanic $mechanic
     *
     * @return $this
     */
    public function setMechanic(Mechanic $mechanic)
    {
        $this->mechanic = $mechanic;

        return $this;
    }

    /**
     * Return the path for storing container config files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->path.'/config';
    }

    /**
     * Return the path of the database file.
     *
     * @return string
     */
    public function databaseFile()
    {
        return $this->path.'/database.sqlite';
    }

    /**
     * Return the path of the docker-compose file.
     *
     * @return string
     */
    public function dockerComposeFile()
    {
        return $this->path.'/docker-compose.yaml';
    }

    /**
     * Return the path of additional docker images.
     *
     * @return string
     */
    public function dockerImagesPath()
    {
        return $this->path.'/image-sets';
    }

    /**
     * Return the path where our generated SSL certs live.
     *
     * @return string
     */
    public function sslPath()
    {
        return $this->path.'/ssl';
    }

    /**
     * Return the library path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Return the path for our additional/customised views
     * For example NGiNX config/ docker-compose views
     * for alternative container structures.
     *
     * @return string
     */
    public function viewsPath()
    {
        return $this->path.'/views';
    }

    /**
     * Check if the library has previously been set up.
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        return $this->path && $this->files->exists($this->path);
    }

    /**
     * Set up the library, by creating files, storing the path in .env
     * creating the sqlite database and updating the app config.
     *
     * @param Application $app
     * @param bool        $force
     *
     * @throws PorterSetupFailed
     */
    public function setUp(Application $app, $force = false)
    {
        if ($this->alreadySetUp() && !$force) {
            throw new PorterSetupFailed(
                "The porter library already exists at '{$this->path}'. ".
                'You can use the --force flag to continue.'
            );
        }

        if (!$this->path) {
            $this->path = $this->mechanic->getUserHomePath().'/.porter';

            $this->moveExistingConfig();
            $this->publishEnv();
            $this->updateEnv();
        }

        if (!$this->path) {
            throw new PorterSetupFailed('Failed detecting and setting the library path for Porter.');
        }

        $this->publishConfigFiles();
        $this->createDirectoryStructure();
        $this->createDatabase();
        $this->updateAppConfig($app);

        if ($this->shouldMigrateAndSeedDatabase) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed');
        }

        $app->instance(self::class, $this);
    }

    /**
     * Publish the .env.example file to .env.
     *
     * @throws PorterSetupFailed
     */
    protected function publishEnv()
    {
        try {
            $this->filePublisher->publish(base_path('.env.example'), base_path('.env'));
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed publishing the .env file');
        }
    }

    /**
     * Move any existing config at the path to a backup directory
     * So we can avoid wiping out data/settings completely.
     */
    protected function moveExistingConfig()
    {
        if (!$this->alreadySetUp()) {
            return;
        }

        $this->files->moveDirectory($this->path, $this->path.'_'.now()->format('YmdHis'));
    }

    /**
     * Create the sqlite database.
     */
    protected function createDatabase()
    {
        $this->files->put($this->databaseFile(), '');
    }

    /**
     * Update the .env file values with the new library path.
     *
     * @throws PorterSetupFailed
     */
    protected function updateEnv()
    {
        try {
            $envContent = $this->files->get(base_path('.env'));
            $envContent = preg_replace('/LIBRARY_PATH=.*\n/', "LIBRARY_PATH=\"{$this->path}\"\n", $envContent);
            $this->files->put(base_path('.env'), $envContent);
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed changing library path in the .env file', 0, $e);
        }
    }

    /**
     * Update core parts of the app config.
     *
     * @param Application $app
     */
    protected function updateAppConfig(Application $app)
    {
        $app['config']->set('database.connections.sqlite.database', $this->databaseFile());
        $app['config']->set('porter.library_path', $this->path);
    }

    /**
     * Publish the container config files to the library config dir.
     *
     * @throws PorterSetupFailed
     */
    protected function publishConfigFiles()
    {
        try {
            $this->filePublisher->publish(resource_path('stubs/config'), $this->configPath());
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed publishing the container configuration files');
        }
    }

    /**
     * Make sure we don't try to seed and migrate (usually in tests).
     *
     * @return $this
     */
    public function dontMigrateAndSeedDatabase()
    {
        $this->shouldMigrateAndSeedDatabase = false;

        return $this;
    }

    /**
     * Create the directory structure in the library path.
     */
    protected function createDirectoryStructure()
    {
        $directories = [$this->sslPath(), $this->viewsPath().'/nginx'];

        foreach ($directories as $directory) {
            if (!$this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        }
    }

    /**
     * Return the Mechanic instance.
     *
     * @return Mechanic
     */
    public function getMechanic()
    {
        return $this->mechanic;
    }
}
