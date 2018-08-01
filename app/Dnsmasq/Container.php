<?php

namespace App\Dnsmasq;


class Container
{
    public function updateDomain($from, $to)
    {
        $filePath = config('app.config_storage_path').'/dnsmasq/dnsmasq.conf';

        $newConfig = preg_replace("/\/.{$from}\//", "/.{$to}/", file_get_contents($filePath));

        file_put_contents($filePath, $newConfig);
    }
}
