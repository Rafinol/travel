<?php

namespace App\Providers;

use App\Services\Travel\FlightTravelService;
use App\Services\Travel\Agregators\YandexFlightTravelService;
use App\UseCases\Trip\Departure\DefaultTripService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\Departure\CustomTripService;
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
        $this->app->singleton(FlightTravelService::class, YandexFlightTravelService::class);
        //$this->app->singleton(DepartureService::class, DefaultTripService::class);
        $this->app->singleton(DepartureService::class, CustomTripService::class);
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
