<?php

namespace App\Support\Mechanics;


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
    public function trustCertificate($crt);

    /**
     * Return the User's home directory path
     *
     * @return string
     */
    public function getUserHomePath();

    /**
     * Flush the host system DNS cache
     *
     * @return void
     */
    public function flushDns();
}
