<?php

namespace Tests;

use App\Porter;
use Illuminate\Support\ServiceProvider;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->publishes([resource_path('stubs/config') => config('porter.library_path')]);
    }
}
