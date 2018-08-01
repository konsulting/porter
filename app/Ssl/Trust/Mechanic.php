<?php

namespace App\Ssl\Trust;


interface Mechanic
{
    /**
     * Trust the given root certificate file.
     *
     * @param  string  $pem
     * @return void
     */
    public function trustCA($pem);

    /**
     * Trust the given certificate file.
     *
     * @param  string  $crt
     * @return void
     */
    public function trustCertificate($url);
}
