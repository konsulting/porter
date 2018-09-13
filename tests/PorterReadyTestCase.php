<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

class PorterReadyTestCase extends BaseTestCase
{
    protected function performMigrations()
    {
        Artisan::call('migrate:fresh');
        Artisan::call('vendor:publish', ['--provider' => AppServiceProvider::class]);
    }

}
