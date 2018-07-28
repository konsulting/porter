<?php

namespace App\DockerCompose;

use App\Porter;
use Illuminate\Config\Repository;

class YamlBuilder
{
    protected $config;
    protected $path;
    protected $stubsPath;

    public function __construct(Repository $config)
    {
        $this->path = $config->get('docker_compose.path');
        $this->stubsPath = $config->get('docker_compose.stubs_path');
    }

    public function build()
    {
        file_put_contents(
            base_path('docker-compose.yaml'),
            view('docker_compose.base')
                ->with(app(Porter::class)->getSettings()->toArray())
                ->render()
        );
    }
}
