<?php
// @formatter:off

/**
 * A helper file for Laravel 5, to provide autocomplete information to your IDE
 * Generated for Laravel 1.0.0-alpha.1 on 2018-09-11 21:58:36.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */

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

    class SiteConfBuilder extends \App\Support\Nginx\SiteConfBuilderFacade {}
 
}



