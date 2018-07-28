<?php

namespace App\Ssl;

class CertificateBuilder
{
    protected $certificatesPath;
    protected $caKeyPath;
    protected $caPemPath;
    protected $caSrlPath;
    protected $oName;
    protected $cName;
    protected $email;

    public function __construct($certificatesPath)
    {
        $this->certificatesPath = $certificatesPath;

        $this->caKeyPath = $this->certificatesPath.'/KleverPorterSelfSigned.key';
        $this->caPemPath = $this->certificatesPath.'/KleverPorterSelfSigned.pem';
        $this->caSrlPath = $this->certificatesPath.'/KleverPorterCASelfSigned.srl';

        $this->oName = 'Klever Porter CA Self Signed Organization';
        $this->cName = 'Klever Porter CA Self Signed CN';
        $this->email = 'rootcertificate@klever.porter';
    }

    public function build($url)
    {
        $this->createCa();
        $this->createCertificate($url);
    }

    public function createCa()
    {
        if (file_exists($this->caKeyPath) || file_exists($this->caPemPath)) {
            return;
        }

        exec(sprintf(
            'openssl req -new -newkey rsa:2048 -days 730 -nodes -x509 -subj "/C=/ST=/O=%s/localityName=/commonName=%s/organizationalUnitName=Developers/emailAddress=%s/" -keyout %s -out %s',
            $this->oName, $this->cName, $this->email, $this->caKeyPath, $this->caPemPath
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
        $keyPath = $this->certificatesPath.'/'.$url.'.key';
        $csrPath = $this->certificatesPath.'/'.$url.'.csr';
        $crtPath = $this->certificatesPath.'/'.$url.'.crt';
        $confPath = $this->certificatesPath.'/'.$url.'.conf';

        $this->createConf($confPath, $url);
        $this->createPrivateKey($keyPath);
        $this->createSigningRequest($url, $keyPath, $csrPath, $confPath);

        $caSrlParam = ' -CAcreateserial';
        if (file_exists($this->caSrlPath)) {
            $caSrlParam = ' -CAserial ' . $this->caSrlPath;
        }

        exec(sprintf(
            'openssl x509 -req -sha256 -days 730 -CA %s -CAkey %s%s -in %s -out %s -extensions v3_req -extfile %s',
            $this->caPemPath, $this->caKeyPath, $caSrlParam, $csrPath, $crtPath, $confPath
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
     * @param  string  $keyPath
     * @return void
     */
    public function createSigningRequest($url, $keyPath, $csrPath, $confPath)
    {
        exec(sprintf(
            'openssl req -new -key %s -out %s -subj "/C=/ST=/O=/localityName=/commonName=%s/organizationalUnitName=/emailAddress=%s%s/" -config %s',
            $keyPath, $csrPath, $url, $url, '@klever.porter', $confPath
        ));
    }
}
