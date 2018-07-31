<?php

namespace App\Nginx;

use App\Site;

class SiteConfBuilder
{
    /**
     * Build the nginx.conf file for a given site
     *
     * @param Site $site
     * @throws \Throwable
     */
    public function build(Site $site)
    {
        file_put_contents(
            $site->nginx_conf_path,
            view($site->nginx_conf_template)->with([
                'site' => $site->url,
                'name' => $site->name,
                'version' => $site->php_version->safe
            ])->render()
        );
    }

    /**
     * Destroy the nginx.conf conf for a given site
     *
     * @param Site $site
     */
    public function destroy(Site $site)
    {
        @unlink($site->nginx_conf_path);
    }
}
