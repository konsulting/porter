<?php

namespace App\Providers;

use App\Support\Contracts\Env;
use Illuminate\Support\ServiceProvider;

class DevelopmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment() === Env::DEVELOPMENT) {
            return;
        }

        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
