<?php

namespace App\Support\Valet;

use App\Models\Setting;
use App\Models\Site;
use App\Porter;
use App\Support\Console\Cli;
use App\Support\Console\ConsoleWriter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Valet
{
    /** @var Porter */
    protected $porter;

    /** @var Cli */
    protected $cli;

    /** @var ConsoleWriter */
    protected $writer;

    /** @var bool hasWarned */
    protected $hasWarned;

    protected const HTTP_PORT = 80;
    protected const HTTPS_PORT = 443;
    protected const COMPAT_HTTP_PORT = 8008; // 8080 is needed by the node container at the moment
    protected const COMPAT_HTTPS_PORT = 8443;

    public function __construct(Porter $porter, Cli $cli, ConsoleWriter $writer)
    {
        $this->porter = $porter;
        $this->cli = $cli;
        $this->writer = $writer;
    }

    public function turnOn()
    {
        if (setting('use_valet', 'off') === 'on') {
            $this->writer->info('Valet compatibility already complete');

            return;
        }

        $this->sudoWarning();

        Setting::updateOrCreate('valet', 'on');
        Setting::updateOrCreate('http_port', static::COMPAT_HTTP_PORT);
        Setting::updateOrCreate('https_port', static::COMPAT_HTTPS_PORT);

        Artisan::call('dns:off');
        $this->cli->execRealTime('valet start');

        $this->porter->compose();
        $this->porter->restart();

        // Run through all the porter sites and set up a proxy though valet...
        Site::all()->each(function (Site $site) {
            $this->addSite($site);
        });

        $this->writer->line('Completed setting up valet compatibility');
    }

    public function turnOff()
    {
        if (setting('use_valet', 'off') === 'off') {
            $this->writer->info('Valet compatibility already off');

            return;
        }

        $this->sudoWarning();

        // Run through all the porter sites and set up a proxy though valet...
        Site::all()->each(function (Site $site) {
            $this->removeSite($site);
        });

        Setting::updateOrCreate('valet', 'off');
        Setting::updateOrCreate('http_port', static::HTTP_PORT);
        Setting::updateOrCreate('https_port', static::HTTPS_PORT);

        $this->cli->execRealTime('valet stop');
        $this->cli->execRealTime('sudo brew services stop dnsmasq');

        Artisan::call('dns:on');

        $this->porter->compose();
        $this->porter->restart();

        $this->writer->line('Completed removing valet compatibility');
    }

    public function addSite(Site $site)
    {
        $this->sudoWarning();

        if ($this->isProxied($site)) {
            $this->removeSite($site);
        }

        $port = $site->secure ? static::COMPAT_HTTPS_PORT : static::COMPAT_HTTP_PORT;
        $protocol = $site->secure ? 'https://' : 'http://';

        $this->cli->exec("valet proxy {$site->name} {$protocol}127.0.0.1:{$port}");

        $this->writer->line("Added {$site->name} proxy for Valet");
    }

    public function removeSite(Site $site)
    {
        $this->sudoWarning();

        $this->cli->exec("valet unproxy {$site->name}");

        $this->writer->line("Removed Valet proxy for {$site->name}");
    }

    public function listSites()
    {
        $this->sudoWarning();

        return $this->cli->exec('valet proxies');
    }

    public function isProxied(Site $site)
    {
        return Str::contains($this->listSites(), $site->name);
    }

    protected function sudoWarning()
    {
        if ($this->hasWarned) {
            return;
        }

        $this->writer->info('Requires Sudo permissions for Valet');
        $this->hasWarned = true;
    }
}
