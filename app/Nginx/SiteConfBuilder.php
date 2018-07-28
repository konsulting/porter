<?php

namespace App\Nginx;

use Illuminate\Config\Repository;

class SiteConfBuilder
{
    protected $config;
    protected $path;
    protected $stubsPath;

    public function __construct(Repository $config)
    {
        $this->path = $config->get('nginx.path');
        $this->stubsPath = $config->get('nginx.stubs_path');
    }

    public function build($project)
    {
        $conf = 'nginx.base.domain' . (($project['secure'] ?? false) ? '_secure' : '');

        file_put_contents(
            storage_path("nginx/conf.d/{$project['name']}.conf"),
            view($conf)
                ->with([
                    'site' => $project['name'].'.'.settings('tld'),
                    'name' => $project['name'],
                    'version' => $project['php']
                ])
                ->render()
        );
    }
}
