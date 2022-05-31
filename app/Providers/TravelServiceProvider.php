<?php

namespace App\Providers;

use App\Services\Travel\BusMock\BusMockTravelService;
use App\Services\Travel\BusRideTravelService;
use App\Services\Travel\FlightTravelService;
use App\Services\Travel\Yandex\YandexTravelService;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Way\TravelWaysService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Http;
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
        $this->app->singleton(FlightTravelService::class, YandexTravelService::class);
        $this->app->singleton(BusRideTravelService::class, BusMockTravelService::class);
        $this->app->singleton(TravelWaysService::class, function ($app) {
            return new TravelWaysService($app->make('config')->get('travelways')['additional_routes'] ?? []);
        });
        $this->app->singleton(BusTripService::class, function ($app) {
            return new BusTripService(
                $app->make('config')->get('travelways')['exclusive_bus_pairs'] ?? [],
                $app->make(BusRideTravelService::class)
            );
        });
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
