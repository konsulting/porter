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
use App\Support\Images\Organiser\Organiser;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->getFinder()->prependLocation(app(PorterLibrary::class)->viewsPath());

        $this->app[ImageSetRepositoryContract::class]->registerViewNamespaces($this->app);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(
                app(CliContract::class),
                app(Filesystem::class),
                app(PorterLibrary::class)->sslPath()
            );
        });

        $this->app->bind(ConsoleWriter::class);

        $this->app->bind(CliContract::class, Cli::class);

        $this->app->bind(ImageSetRepositoryContract::class, function () {
            return new ImageSetRepository([
                resource_path('image_sets'),
                app(PorterLibrary::class)->dockerImagesPath(),
            ]);
        });

        $this->app->bind(Organiser::class, function () {
            return new Organiser(
                app(Porter::class)->getDockerImageSet(),
                app(CliContract::class),
                app(FileSystem::class)
            );
        });

        $this->app->singleton(Porter::class);

        $this->app->singleton(PorterLibrary::class, function () {
            return new PorterLibrary(app(FilePublisher::class), config('porter.library_path'));
        });

        $this->app->singleton(ServerBag::class);
    }
}
