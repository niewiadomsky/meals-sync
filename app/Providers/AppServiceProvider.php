<?php

namespace App\Providers;

use App\Clients\TheMealDbClient;
use App\Services\MealImporter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TheMealDbClient::class, function ($app) {
            return new TheMealDbClient(config('services.themealdb.base_url'));
        });

        $this->app->bind(MealImporter::class, function ($app) {
            return new MealImporter($app->make(TheMealDbClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
