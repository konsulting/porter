<?php

namespace App\Events;

use App\Models\Site;

class SiteRemoved
{
    public $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }
}
