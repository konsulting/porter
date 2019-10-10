<?php

namespace App\Support\Mechanics;

interface Mechanic
{
    /**
     * Trust the given root certificate file.
     *
     * @param string $pem
     *
     * @return void
     */
    public function trustCA($pem);

    /**
     * Trust the given certificate file.
     *
     * @param string $crt
     *
     * @return void
     */
    public function trustCertificate($crt);

    /**
     * Return the User's home directory path.
     *
     * @return string
     */
    public function getUserHomePath();

    /**
     * Flush the host system DNS cache.
     *
     * @return void
     */
    public function flushDns();

    /**
     * Add the alternative loopback address to the system.
     *
     * @return void
     */
    public function addAlternativeLoopbackAddress();

    /**
     * Remove the alternative loopback address from the system.
     *
     * @return void
     */
    public function removeAlternativeLoopbackAddress();

    /**
     * Get standard loopback address.
     *
     * @return string
     */
    public function getStandardLoopback();

    /**
     * Get alternative loopback address.
     *
     * @return string
     */
    public function getAlternativeLoopback();

    /**
     * Does a Porter domain resolve to the standard loopback address.
     *
     * @return bool
     */
    public function isUsingAlternativeLoopback();

    /**
     *  Does a Porter domain resolve to the standard loopback address?
     *
     * @return bool
     */
    public function isUsingStandardLoopback();

    /**
     * Determine the working IP for Porter.
     *
     * @return string
     */
    public function getPorterDomainIp();
}
