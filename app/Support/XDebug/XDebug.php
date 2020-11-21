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

        foreach (PhpVersion::active()->get() as $version) {
            $move = "mv /etc/php/{$version->version_number}/mods-available/xdebug.bak /etc/php/{$version->version_number}/mods-available/xdebug.ini";

            $this->dockerCompose->execContainer($version->getFpmNameAttribute())->append($move)->interactive()->perform();
        }

        foreach (PhpVersion::active()->get() as $version) {
            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }

    public function turnOff()
    {
        Setting::updateOrCreate('use_xdebug', 'off');

        foreach (PhpVersion::active()->get() as $version) {
            $move = "mv /etc/php/{$version->version_number}/mods-available/xdebug.ini /etc/php/{$version->version_number}/mods-available/xdebug.bak";

            $this->dockerCompose->execContainer($version->getFpmNameAttribute())->append($move)->interactive()->perform();
        }

        foreach (PhpVersion::active()->get() as $version) {
            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }
}
