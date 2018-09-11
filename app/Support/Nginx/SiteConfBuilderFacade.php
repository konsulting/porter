<?php

namespace App\Support\Nginx;

use Illuminate\Support\Facades\Facade;

class SiteConfBuilderFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SiteConfBuilder::class;
    }
}
