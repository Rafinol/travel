<?php

namespace Tests\Unit;

use App\Models\City\City;
use App\Models\Route\PartRoute;
use App\Models\Route\RouteSearchForm;
use App\Dto\RouteDto\ResultRouteDto;
use App\Dto\RouteDto\RouteDto;
use App\Services\Travel\Yandex\MockYandexFlightTravelService;
use App\Services\Travel\FlightTravelService;
use App\UseCases\Trip\Route\CreateRoutesService;
use App\UseCases\Trip\Route\SearchRoutesService;
use App\UseCases\Trip\RouteDtoCleanerService;
use App\UseCases\Trip\Type\AviaTripService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_search_Kazan_to_Rome_response()
    {
        \App::singleton(FlightTravelService::class, MockYandexFlightTravelService::class);
        /** @var SearchRoutesService $service*/
        $service = \App::make(SearchRoutesService::class);
        $departure_city = City::where('name', 'Kazan')->first();
        $arrival_city = City::where('name', 'Rome')->first();
        $date = Carbon::tomorrow();
        $search_form = $service->getOrCreateSearchForm($departure_city, $arrival_city, $date);
        $this->assertInstanceOf(RouteSearchForm::class, $search_form);
        $routes = $service->search($search_form);
        $this->assertInstanceOf(ResultRouteDto::class, $routes[0]);

        /** @var CreateRoutesService $service*/
        $service = \App::make(CreateRoutesService::class);
        $routes = RouteDtoCleanerService::getTopRoutes($routes, 1);
        $service->saveRoutes($search_form, $routes);
        /**@var RouteDto $raw_part_route*/
        $raw_part_route = $routes[0]->routes[0];
        $part_route = PartRoute::where('from_id', $raw_part_route->departure_point->code)
            ->where('to_id', $raw_part_route->arrival_point->code)
            ->where('sdate',$raw_part_route->departure_date)
            ->where('edate', $raw_part_route->arrival_date)
            ->first();
        $this->assertInstanceOf(PartRoute::class, $part_route);
    }
}
