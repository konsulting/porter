<?php

namespace App\Support\Ssl;

use App\Support\Contracts\Cli;
use App\Support\Mechanics\ChooseMechanic;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class CertificateBuilder
{
    protected $cli;
    protected $filesystem;
    protected $certificatesPath;
    protected $oName;
    protected $cName;
    protected $domain;
    protected $email;

    public function __construct(Cli $cli, Filesystem $filesystem, $certificatesPath)
    {
        $this->cli = $cli;
        $this->filesystem = $filesystem;
        $this->certificatesPath = $certificatesPath;

        $this->oName = 'Klever Porter CA Self Signed Organization';
        $this->cName = 'Klever Porter CA Self Signed CN';
        $this->domain = 'klever.porter';
        $this->email = 'rootcertificate@'.$this->domain;
    }

    /**
     * List the certificate authority paths.
     *
     * @return object
     */
    public function caPaths()
    {
        return (object) [
            'key' => $this->certificatesPath.'/KleverPorterCASelfSigned.key',
            'pem' => $this->certificatesPath.'/KleverPorterCASelfSigned.pem',
            'srl' => $this->certificatesPath.'/KleverPorterCASelfSigned.srl',
        ];
    }

    /**
     * List paths based on the certificate url.
     *
     * @param $url
     *
     * @return object
     */
    public function paths($url)
    {
        return (object) [
            'key'  => $this->certificatesPath.'/'.$url.'.key',
            'csr'  => $this->certificatesPath.'/'.$url.'.csr',
            'crt'  => $this->certificatesPath.'/'.$url.'.crt',
            'conf' => $this->certificatesPath.'/'.$url.'.conf',
        ];
    }

    /**
     * Build a certificate based on the url.  Create CA if needed.
     *
     * @param $url
     */
    public function build($url)
    {
        $this->destroy($url);
        $this->createCa();
        $this->createCertificate($url);
    }

    /**
     * Destroy certificate for site based on url.
     *
     * @param $url
     */
    public function destroy($url)
    {
        foreach ($this->paths($url) as $path) {
            $this->filesystem->delete($path);
        }
    }

    /**
     * Create certificate authority.
     */
    public function createCa()
    {
        $paths = $this->caPaths();

        if ($this->filesystem->exists($paths->key) || $this->filesystem->exists($paths->pem)) {
            return;
        }

        $this->cli->exec(sprintf(
            'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/C=GB/ST=Berks/O=%s/localityName=Reading/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout %s -out %s',
            $this->oName, $this->cName, $this->email, $paths->key, $paths->pem
        ));

        ChooseMechanic::forOS()->trustCA($paths->pem);
    }

    /**
     * Create a certificate for the given URL.
     *
     * @param string $url
     *
     * @return void
     */
    public function createCertificate($url)
    {
        $paths = $this->paths($url);
        $caPaths = $this->caPaths();

        $this->createConf($paths->conf, $url);
        $this->createPrivateKey($paths->key);
        $this->createSigningRequest($url, $paths->key, $paths->csr, $paths->conf);

        $caSrlParam = ' -CAcreateserial';
        if ($this->filesystem->exists($caPaths->srl)) {
            $caSrlParam = ' -CAserial '.$caPaths->srl;
        }

        $this->cli->exec(sprintf(
            'openssl x509 -req -sha256 -days 730 -CA %s -CAkey %s%s -in %s -out %s -extensions v3_req -extfile %s',
            $caPaths->pem, $caPaths->key, $caSrlParam, $paths->csr, $paths->crt, $paths->conf
        ));

        // Trusting the certificate shouldn't be necessary once the CA is trusted.
        // ChooseMechanic::forOS()->trustCertificate($paths->crt);
    }

    /**
     * Build the SSL config for the given URL.
     *
     * @param $path
     * @param $url
     */
    public function createConf($path, $url)
    {
        $this->filesystem->put($path, view('ssl.conf')->withUrl($url)->render());
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param string $keyPath
     *
     * @return void
     */
    public function createPrivateKey($keyPath)
    {
        $this->cli->exec(sprintf('openssl genrsa -out %s 2048', $keyPath));
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param $url
     * @param string $keyPath
     * @param $csrPath
     * @param $confPath
     *
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath)
    {
        $this->cli->exec(sprintf(
            'openssl req -new -key %s -out %s -subj "/C=GB/ST=Berks/O=%s/localityName=Reading/commonName=%s/organizationalUnitName=Developers/emailAddress=%s%s/" -config %s',
            $keyPath, $csrPath, $this->domain, $url, $url, '@'.$this->domain, $confPath
        ));
    }

    /**
     * Clear generated certs. Optionally clear CA too.
     *
     * @param bool $dropCA
     */
    public function clearCertificates($dropCA = false)
    {
        $caPaths = (array) $this->caPaths();

        $files = $this->filesystem
            ->allFiles($this->certificatesPath);

        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            if (!$dropCA && in_array($file->getPathname(), $caPaths)) {
                continue;
            }

            $this->filesystem->delete($file->getPathname());
        }
    }
}
