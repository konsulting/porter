<?php

namespace App;

use App\Support\FilePublisher;
use App\Support\Mechanics\ChooseMechanic;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class PorterLibrary
{
    /** @var string */
    protected $path;

    /** @var Filesystem */
    protected $files;

    /** @var \App\Support\FilePublisher **/
    protected $filePublisher;

    protected $shouldMigrateAndSeedDatabase = true;

    public function __construct(FilePublisher $filePublisher, $path)
    {
        $this->filePublisher = $filePublisher;
        $this->files = $filePublisher->getFilesystem();
        $this->path = $path;
    }

    public function configPath()
    {
        return $this->path.'/config';
    }

    public function databaseFile()
    {
        return $this->path.'/database.sqlite';
    }

    public function dockerComposeFile()
    {
        return $this->path.'/docker-compose.yaml';
    }

    public function dockerImagesPath()
    {
        return $this->path.'/docker';
    }

    public function sslPath()
    {
        return $this->path.'/ssl';
    }

    public function path()
    {
        return $this->path;
    }

    public function viewsPath()
    {
        return $this->path.'/views';
    }

    public function alreadySetUp()
    {
        return $this->path && $this->files->exists($this->path);
    }

    public function setUp(Application $app, $force = false)
    {
        if ($this->alreadySetUp() && ! $force) {
            return;
        }

        if (! $this->path) {
            $this->path = ChooseMechanic::forOS()->getUserHomePath().'/.porter';

            $this->moveExistingConfig();
            $this->publishEnv();
            $this->updateEnv();
        }

        $this->publishConfigFiles();
        $this->createDatabase();
        $this->updateAppConfig($app);

        if ($this->shouldMigrateAndSeedDatabase) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed');
        }

        $app->instance(PorterLibrary::class, $this);
    }

    protected function publishEnv()
    {
        $this->filePublisher->publish(
            base_path('.env.example'),
            base_path('.env')
        );
    }

    protected function moveExistingConfig()
    {
        if (! $this->alreadySetUp()) {
            return;
        }

        $this->files->moveDirectory($this->path(), $this->path().'_'.now()->format('YmdHis'));
    }

    protected function createDatabase()
    {
        $this->files->put($this->databaseFile(), '');
    }

    protected function updateEnv()
    {
        $envContent = $this->files->get(base_path('.env'));
        $envContent = preg_replace('/LIBRARY_PATH=.*\n/', "LIBRARY_PATH=\"{$this->path()}\"\n", $envContent);
        $this->files->put(base_path('.env'), $envContent);
    }

    protected function updateAppConfig(Application $app)
    {
        $app['config']->set('database.connections.default.database', $this->databaseFile());
        $app['config']->set('porter.library_path', $this->path());
    }

    protected function publishConfigFiles()
    {
        $this->filePublisher->publish(resource_path('stubs/config'), $this->configPath());
    }

    public function dontMigrateAndSeedDatabase()
    {
        $this->shouldMigrateAndSeedDatabase = false;

        return $this;
    }
}
