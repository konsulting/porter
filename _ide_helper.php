<?php
// @formatter:off

/**
 * A helper file for Laravel 5, to provide autocomplete information to your IDE
 * Generated for Laravel 1.0.0-alpha.1 on 2018-09-11 22:13:11.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */

namespace App\Support\Ssl { 

    /**
     * 
     *
     */ 
    class CertificateBuilderFacade {
        
        /**
         * List the certificate authority paths
         *
         * @return object 
         * @static 
         */ 
        public static function caPaths()
        {
            return \App\Support\Ssl\CertificateBuilder::caPaths();
        }
        
        /**
         * List paths based on the certificate url
         *
         * @param $url
         * @return object 
         * @static 
         */ 
        public static function paths($url)
        {
            return \App\Support\Ssl\CertificateBuilder::paths($url);
        }
        
        /**
         * Build a certificate based on the url.  Create CA if needed.
         *
         * @param $url
         * @static 
         */ 
        public static function build($url)
        {
            return \App\Support\Ssl\CertificateBuilder::build($url);
        }
        
        /**
         * Destroy certificate for site based on url
         *
         * @param $url
         * @static 
         */ 
        public static function destroy($url)
        {
            return \App\Support\Ssl\CertificateBuilder::destroy($url);
        }
        
        /**
         * Create certificate authority
         *
         * @static 
         */ 
        public static function createCa()
        {
            return \App\Support\Ssl\CertificateBuilder::createCa();
        }
        
        /**
         * Create a certificate for the given URL.
         *
         * @param string $url
         * @return void 
         * @static 
         */ 
        public static function createCertificate($url)
        {
            \App\Support\Ssl\CertificateBuilder::createCertificate($url);
        }
        
        /**
         * Build the SSL config for the given URL.
         *
         * @param $path
         * @param $url
         * @static 
         */ 
        public static function createConf($path, $url)
        {
            return \App\Support\Ssl\CertificateBuilder::createConf($path, $url);
        }
        
        /**
         * Create the private key for the TLS certificate.
         *
         * @param string $keyPath
         * @return void 
         * @static 
         */ 
        public static function createPrivateKey($keyPath)
        {
            \App\Support\Ssl\CertificateBuilder::createPrivateKey($keyPath);
        }
        
        /**
         * Create the signing request for the TLS certificate.
         *
         * @param $url
         * @param string $keyPath
         * @param $csrPath
         * @param $confPath
         * @return void 
         * @static 
         */ 
        public static function createSigningRequest($url, $keyPath, $csrPath, $confPath)
        {
            \App\Support\Ssl\CertificateBuilder::createSigningRequest($url, $keyPath, $csrPath, $confPath);
        }
        
        /**
         * Clear generated certs. Optionally clear CA too.
         *
         * @param bool $dropCA
         * @static 
         */ 
        public static function clearDirectory($dropCA = false)
        {
            return \App\Support\Ssl\CertificateBuilder::clearDirectory($dropCA);
        }
         
    }
 
}

namespace App\Support\Nginx { 

    /**
     * 
     *
     */ 
    class SiteConfBuilderFacade {
        
        /**
         * Build the nginx.conf file for a given site
         *
         * @param \App\Models\Site $site
         * @throws \Throwable
         * @static 
         */ 
        public static function build($site)
        {
            return \App\Support\Nginx\SiteConfBuilder::build($site);
        }
        
        /**
         * Destroy the nginx.conf conf for a given site
         *
         * @param \App\Models\Site $site
         * @static 
         */ 
        public static function destroy($site)
        {
            return \App\Support\Nginx\SiteConfBuilder::destroy($site);
        }
         
    }
 
}


namespace  { 

    class CertificateBuilder extends \App\Support\Ssl\CertificateBuilderFacade {}

    class SiteConfBuilder extends \App\Support\Nginx\SiteConfBuilderFacade {}
 
}



