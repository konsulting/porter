<?php

namespace App\Providers;

use App\Porter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        $this->app->singleton(Porter::class, function ($app) {
            return new Porter(storage_path('settings.json'));
        });
    }
}
