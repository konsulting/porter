<?php

namespace App\Nginx;

use App\Site;
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

    /**
     * Build the nginx.conf file for a given site
     *
     * @param Site $site
     * @throws \Throwable
     */
    public function build(Site $site)
    {
        $type = $site->nginx_type ?? 'default';
        $conf = "nginx.{$type}.domain" . (($site->secure ?? false) ? '_secure' : '');

        file_put_contents(
            storage_path("config/nginx/conf.d/{$site->name}.conf"),
            view($conf)
                ->with([
                    'site' => $site->url,
                    'name' => $site->name,
                    'version' => $site->php_version->safe
                ])
                ->render()
        );
    }

    /**
     * Destroy the nginx.conf file for a given site
     *
     * @param Site $site
     */
    public function destroy(Site $site)
    {
        @unlink(storage_path("config/nginx/conf.d/{$site->name}.conf"));
    }
}
