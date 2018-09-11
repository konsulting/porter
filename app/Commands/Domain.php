<?php

namespace App\Commands;

use App\Models\Setting;
use App\Support\Dnsmasq\Container as DnsmasqContainer;

class Domain extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'domain {domain?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the domain for Porter sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $domain = $this->argument('domain');

        if (! $domain) {
            $this->info(sprintf("The current domain is '%s'", setting('domain')));
            return;
        }

        $old = setting('domain');

        Setting::where('name', 'domain')->first()->update(['value' => $domain]);

        (new DnsmasqContainer)->updateDomain($old, $domain);

        $this->call('site:renew-certs');
        $this->call('make-files');
    }
}
