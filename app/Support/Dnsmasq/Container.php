<?php

namespace App\Support\Dnsmasq;

use App\PorterLibrary;
use Illuminate\Filesystem\Filesystem;

class Container
{
    /** @var Filesystem */
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function updateDomain($from, $to)
    {
        $filePath = app(PorterLibrary::class)->configPath().'/dnsmasq/dnsmasq.conf';

        $newConfig = preg_replace("/\/.{$from}\//", "/.{$to}/", file_get_contents($filePath));

        $this->files->put($filePath, $newConfig);
    }
}
