<?php

namespace App\Providers;

use App\Services\Travel\TravelService;
use App\Services\Travel\YandexFlightTravelService;
use Illuminate\Support\ServiceProvider;

class TravelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TravelService::class, YandexFlightTravelService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
