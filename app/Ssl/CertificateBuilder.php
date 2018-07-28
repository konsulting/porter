<?php

namespace App\Ssl;

class CertificateBuilder
{
    protected $certificatesPath;
    protected $oName;
    protected $cName;
    protected $domain;
    protected $email;

    public function __construct($certificatesPath)
    {
        $this->certificatesPath = $certificatesPath;

        $this->oName = 'Klever Porter CA Self Signed Organization';
        $this->cName = 'Klever Porter CA Self Signed CN';
        $this->domain = 'klever.porter';
        $this->email = 'rootcertificate@'.$this->domain;
    }

    public function caPaths()
    {
        return (object) [
            'key' => $this->certificatesPath.'/KleverPorterSelfSigned.key',
            'pem' => $this->certificatesPath.'/KleverPorterSelfSigned.pem',
            'srl' => $this->certificatesPath.'/KleverPorterCASelfSigned.srl',
        ];
    }

    public function paths($url)
    {
        return (object) [
            'key' => $this->certificatesPath.'/'.$url.'.key',
            'csr' => $this->certificatesPath.'/'.$url.'.csr',
            'crt' => $this->certificatesPath.'/'.$url.'.crt',
            'conf' => $this->certificatesPath.'/'.$url.'.conf',
        ];
    }

    public function build($url)
    {
        $this->destroy($url);
        $this->createCa();
        $this->createCertificate($url);
    }

    public function destroy($url)
    {
        foreach ($this->paths($url) as $path) {
            @unlink($path);
        }
    }

    public function createCa()
    {
        $paths = $this->caPaths();

        if (file_exists($paths->key) || file_exists($paths->pem)) {
            return;
        }

        exec(sprintf(
            'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/C=/ST=/O=%s/localityName=/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout %s -out %s',
            $this->oName, $this->cName, $this->email, $paths->key, $paths->pem
        ));
    }

    /**
     * Create a certificate for the given URL.
     *
     * @param  string  $url
     * @return void
     */
    public function createCertificate($url)
    {
        $paths = $this->paths($url);
        $caSrlPath = $this->caPaths()->srl;

        $this->createConf($paths->conf, $url);
        $this->createPrivateKey($paths->key);
        $this->createSigningRequest($url, $paths->key, $paths->csr, $paths->conf);

        $caSrlParam = ' -CAcreateserial';
        if (file_exists($caSrlPath)) {
            $caSrlParam = ' -CAserial ' . $caSrlPath;
        }

        exec(sprintf(
            'openssl x509 -req -sha256 -days 730 -CA %s -CAkey %s%s -in %s -out %s -extensions v3_req -extfile %s',
            $this->caPemPath, $this->caKeyPath, $caSrlParam, $paths->csr, $paths->crt, $paths->conf
        ));
    }

    /**
     * Build the SSL config for the given URL.
     *
     * @param $path
     * @param $url
     */
    public function createConf($path, $url)
    {
        file_put_contents($path, view('ssl.conf')->withUrl($url)->render());
    }

    /**
     * Create the private key for the TLS certificate.
     *
     * @param  string  $keyPath
     * @return void
     */
    public function createPrivateKey($keyPath)
    {
        exec(sprintf('openssl genrsa -out %s 2048', $keyPath));
    }

    /**
     * Create the signing request for the TLS certificate.
     *
     * @param $url
     * @param  string $keyPath
     * @param $csrPath
     * @param $confPath
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath)
    {
        exec(sprintf(
            'openssl req -new -key %s -out %s -subj "/C=/ST=/O=/localityName=/commonName=%s/organizationalUnitName=/emailAddress=%s%s/" -config %s',
            $keyPath, $csrPath, $url, $url, '@', $this->domain, $confPath
        ));
    }
}
