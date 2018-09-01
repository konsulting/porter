<?php

namespace App\Providers;

use App\Porter;
use App\Ssl\CertificateBuilder;
use App\Support\Contracts\Cli as CliContract;
use App\Support\Cli;
use App\Support\ConsoleWriter;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use App\Support\Images\ImageSetRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->getFinder()->prependLocation(config('porter.library_path').'/views');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CliContract::class, function () {
            return new Cli;
        });
        $this->app->bind(ImageSetRepositoryContract::class, function () {
            return (new ImageSetRepository)->addLocation(config('porter.library_path').'/docker');
        });

        $this->app->singleton(Porter::class);
        $this->app->singleton(ConsoleWriter::class);
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(config('porter.library_path'));
        });

        $this->publishes([resource_path('stubs/config') => config('porter.library_path').'/config']);
    }
}
