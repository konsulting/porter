<?php

namespace App\Support\XDebug;

use App\Models\PhpVersion;
use App\Models\Setting;
use App\Porter;
use App\Support\Console\DockerCompose\CliCommandFactory;

class XDebug
{
    /** @var Porter */
    protected $porter;

    /** @var CliCommandFactory */
    protected $dockerCompose;

    public function __construct(Porter $porter, CliCommandFactory $dockerCompose)
    {
        $this->porter = $porter;
        $this->dockerCompose = $dockerCompose;
    }

    public function turnOn()
    {
        Setting::updateOrCreate('use_xdebug', 'on');

        $results = [];
        foreach (PhpVersion::active()->get() as $version) {
            $move = "mv /etc/php/{$version->version_number}/mods-available/xdebug.bak /etc/php/{$version->version_number}/mods-available/xdebug.ini";

            ob_start();
            $this->dockerCompose->execContainer($version->getFpmNameAttribute())->append($move)->interactive()->perform();
            $results[$version->id] = ob_get_clean();
        }

        foreach (PhpVersion::active()->get() as $version) {
            if ($results[$version->id]) {
                // If there was any output captured it's because it failed - the file wasn't able to be moved
                // Most likely because it was moved before
                continue;
            }

            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }

    public function turnOff()
    {
        Setting::updateOrCreate('use_xdebug', 'off');

        $results = [];
        foreach (PhpVersion::active()->get() as $version) {
            $move = "mv /etc/php/{$version->version_number}/mods-available/xdebug.ini /etc/php/{$version->version_number}/mods-available/xdebug.bak";

            ob_start();
            $this->dockerCompose->execContainer($version->getFpmNameAttribute())->append($move)->interactive()->perform();
            $results[$version->id] = ob_get_clean();
        }

        foreach (PhpVersion::active()->get() as $version) {
            if ($results[$version->id]) {
                // If there was any output captured it's because it failed - the file wasn't able to be moved
                // Most likely because it was moved before
                continue;
            }

            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }
}
