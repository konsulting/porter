<?php

namespace App\Support\Nginx;

use App\Models\Site;
use Illuminate\Filesystem\Filesystem;

class SiteConfBuilder
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Build the nginx.conf file for a given site.
     *
     * @param \App\Models\Site $site
     *
     * @throws \Throwable
     */
    public function build(Site $site)
    {
        $this->files->makeDirectory(dirname($site->nginx_conf_path), 0755, true, true);
        $this->files->put(
            $site->nginx_conf_path,
            view($site->nginx_conf_template)->with([
                'site'    => $site->url,
                'name'    => $site->name,
                'version' => $site->php_version->safe,
            ])->render()
        );
    }

    /**
     * Destroy the nginx.conf conf for a given site.
     *
     * @param \App\Models\Site $site
     */
    public function destroy(Site $site)
    {
        $this->files->delete($site->nginx_conf_path);
    }
}
