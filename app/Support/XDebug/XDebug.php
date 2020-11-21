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
            if (!$this->enable($version)) {
                // If enable fails it's because it was already enabled
                continue;
            }

            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }

    public function turnOff()
    {
        Setting::updateOrCreate('use_xdebug', 'off');

        foreach (PhpVersion::active()->get() as $version) {
            if (!$this->disable($version)) {
                // If disable fails it's because it was already disabled
                continue;
            }

            $this->porter->softRestart($version->getFpmNameAttribute());
        }
    }

    /**
     * Move the ini file to .ini to enable.
     *
     * @param PhpVersion $version
     *
     * @return bool
     */
    protected function enable(PhpVersion $version)
    {
        return $this->moveIniFile($version, 'xdebug.bak', 'xdebug.ini');
    }

    /**
     * Move the ini file to .bak to disable.
     *
     * @param PhpVersion $version
     *
     * @return bool
     */
    protected function disable(PhpVersion $version)
    {
        return $this->moveIniFile($version, 'xdebug.ini', 'xdebug.bak');
    }

    /**
     * Move the ini file, If it fails to move, return false;
     *
     * @param PhpVersion $version
     * @param $from
     * @param $to
     *
     * @return bool
     */
    protected function moveIniFile(PhpVersion $version, $from, $to)
    {
        $move = "mv /etc/php/{$version->version_number}/mods-available/{$from} /etc/php/{$version->version_number}/mods-available/{$to}";

        // We wrap in a buffer because we don't want everything output to the screen.
        // If exit code !== 0, its because the move failed since the starting file
        // didn't exist
        ob_start();
        $exitCode = $this->dockerCompose->execContainer($version->getFpmNameAttribute())
            ->append($move)
            ->interactive()
            ->perform();
        ob_end_clean();

        return ! (bool) $exitCode;
    }
}
