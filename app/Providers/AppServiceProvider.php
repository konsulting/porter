<?php

namespace App\Providers;

use App\Porter;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\ConsoleWriter;
use App\Support\Console\ServerBag;
use App\Support\Contracts\Cli as CliContract;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use App\Support\FilePublisher;
use App\Support\Images\ImageSetRepository;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->getFinder()->prependLocation(app(PorterLibrary::class)->viewsPath());
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ServerBag::class, function () {
            return new ServerBag;
        });
        $this->app->bind(CliContract::class, function () {
            return new Cli;
        });
        $this->app->bind(ImageSetRepositoryContract::class, function () {
            return (new ImageSetRepository)->addLocation(app(PorterLibrary::class)->dockerImagesPath());
        });

        $this->app->singleton(PorterLibrary::class, function () {
            return new PorterLibrary(app(FilePublisher::class), config('porter.library_path'));
        });

        $this->app->singleton(Porter::class);
        $this->app->singleton(ConsoleWriter::class);
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(app(PorterLibrary::class)->sslPath());
        });
    }
}
