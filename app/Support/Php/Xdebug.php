<?php

namespace App\Support\Php;

use App\Models\PhpVersion;
use App\PorterLibrary;
use Illuminate\Filesystem\Filesystem;

class Xdebug
{
    /** @var PorterLibrary */
    protected $porterLibrary;
    /** @var Filesystem */
    protected $filesystem;

    protected $activationSwitches = [
        'xdebug.remote_enable',
        'xdebug.default_enable',
    ];

    public function __construct(PorterLibrary $porterLibrary, Filesystem $filesystem)
    {
        $this->porterLibrary = $porterLibrary;
        $this->filesystem = $filesystem;
    }

    /**
     * Turn Xdebug on for all PHP versions, on the one passed.
     * We are just editing the config, rather than disabling wholesale.
     *
     * @param PhpVersion|null $phpVersion
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function turnOn(PhpVersion $phpVersion = null)
    {
        $phpVersions = $phpVersion ? collect([$phpVersion]) : PhpVersion::all();
        $config = $this->porterLibrary->configPath() . '/';

        foreach ($phpVersions as $phpVersion) {
            $this->enable($config . $phpVersion->cli_name);
            $this->enable($config . $phpVersion->fpm_name);
        }
    }

    /**
     * Turn Xdebug off for all PHP versions, on the one passed.
     * We are just editing the config, rather than disabling wholesale.
     *
     * @param PhpVersion|null $phpVersion
     */
    public function turnOff(PhpVersion $phpVersion = null)
    {
        $phpVersions = $phpVersion ? collect([$phpVersion]) : PhpVersion::all();
        $config = $this->porterLibrary->configPath() . '/';

        foreach ($phpVersions as $phpVersion) {
            $this->disable($config . $phpVersion->cli_name);
            $this->disable($config . $phpVersion->fpm_name);
        }
    }

    /**
     * Edit the xdebug.ini file to enable the xdebug
     *
     * @param string $path
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function enable(string $path)
    {
        $file = $path. '/xdebug.ini';

        $contents = $this->filesystem->get($file);

        foreach ($this->activationSwitches as $key) {
            $contents = str_replace($key.'=0', $key.'=1', $contents);
        }

        $this->filesystem->put($file, $contents);
    }

    /**
     * Edit the xdebug.ini file to disable xdebug
     *
     * @param string $path
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function disable(string $path)
    {
        $file = $path. '/xdebug.ini';

        $contents = $this->filesystem->get($file);

        foreach ($this->activationSwitches as $key) {
            $contents = str_replace($key.'=1', $key.'=0', $contents);
        }

        $this->filesystem->put($file, $contents);
    }
}
