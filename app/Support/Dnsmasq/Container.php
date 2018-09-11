<?php

namespace App\Support\Dnsmasq;


class Container
{
    public function updateDomain($from, $to)
    {
        $filePath = config('porter.library_path').'/config/dnsmasq/dnsmasq.conf';

        $newConfig = preg_replace("/\/.{$from}\//", "/.{$to}/", file_get_contents($filePath));

        file_put_contents($filePath, $newConfig);
    }
}
