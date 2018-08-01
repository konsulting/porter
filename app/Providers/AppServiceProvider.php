<?php

namespace App\Providers;

use App\Porter;
use App\Ssl\CertificateBuilder;
use App\Support\ConsoleWriter;
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
        $this->app->singleton(Porter::class);
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(config('app.ssl_storage_path'));
        });
        $this->app->singleton(ConsoleWriter::class);

        $this->publishes([resource_path('stubs/config') => storage_path('config')]);
    }
}
