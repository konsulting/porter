<?php

namespace App\Providers;

use App\Porter;
use App\Ssl\CertificateBuilder;
use App\Support\Contracts\Cli as CliContract;
use App\Support\Cli;
use App\Support\ConsoleWriter;
use App\Support\Images\ImageRepository;
use App\Support\Contracts\ImageRepository as ImageRepositoryContract;
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
        $this->app->bind(CliContract::class, function () {
            return new Cli;
        });
        $this->app->bind(ImageRepositoryContract::class, function () {
            return new ImageRepository;
        });

        $this->app->singleton(Porter::class);
        $this->app->singleton(ConsoleWriter::class);
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(config('app.ssl_storage_path'));
        });

        $this->publishes([resource_path('stubs/config') => storage_path('config')]);
    }
}
