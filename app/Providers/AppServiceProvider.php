<?php

namespace App\Providers;

use App;
use App\Clients\TheMealDbClient;
use App\Services\MealImporter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use URL;

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
        JsonResource::withoutWrapping();
        if (!App::environment(['local', 'testing'])) {
            URL::forceScheme('https');
        }
    }
}
