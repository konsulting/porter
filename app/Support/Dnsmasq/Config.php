<?php

namespace App\Support\Dnsmasq;

use App\PorterLibrary;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class Config
{
    /** @var Filesystem */
    protected $files;

    public function __construct(Filesystem $files, private readonly PorterLibrary $porterLibrary)
    {
        $this->files = $files;
    }

    public function updateDomain($from, $to)
    {
        $newConfig = preg_replace("/\/.{$from}\//", "/.{$to}/", (string) $this->getConfig());

        $this->putConfig($newConfig);
    }

    public function updateIp($to)
    {
        $pattern = "/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/";
        $newConfig = preg_replace($pattern, "/{$to}", (string) $this->getConfig());

        $this->putConfig($newConfig);
    }

    protected function getPath()
    {
        return $this->porterLibrary->configPath().'/dnsmasq/dnsmasq.conf';
    }

    protected function getConfig()
    {
        try {
            return $this->files->get($this->getPath());
        } catch (FileNotFoundException) {
            return '';
        }
    }

    protected function putConfig($content)
    {
        $this->files->makeDirectory(dirname((string) $this->getPath()), 0755, true, true);
        $this->files->put($this->getPath(), $content);
    }
}
