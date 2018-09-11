<?php

namespace App\Support\Ssl;

use Illuminate\Support\Facades\Facade;

class CertificateBuilderFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CertificateBuilder::class;
    }
}
