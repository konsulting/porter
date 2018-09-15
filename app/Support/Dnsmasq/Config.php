<?php

namespace App\Support\Dnsmasq;

use App\PorterLibrary;
use Illuminate\Filesystem\Filesystem;

class Config
{
    /** @var Filesystem */
    protected $files;

    /** @var PorterLibrary */
    private $porterLibrary;

    public function __construct(Filesystem $files, PorterLibrary $porterLibrary)
    {
        $this->files = $files;
        $this->porterLibrary = $porterLibrary;
    }

    public function updateDomain($from, $to)
    {
        $filePath = $this->porterLibrary->configPath().'/dnsmasq/dnsmasq.conf';

        $newConfig = preg_replace("/\/.{$from}\//", "/.{$to}/", file_get_contents($filePath));

        $this->files->put($filePath, $newConfig);
    }
}
