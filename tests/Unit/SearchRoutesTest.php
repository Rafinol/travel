<?php

namespace Tests\Unit;

use App\Models\City\City;
use App\Models\RouteDto\ResultRouteDto;
use App\UseCases\Trip\RoutesSearchService;
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
        $service = \App::make(RoutesSearchService::class);
        $departure_city = City::where('name', 'Kazan')->first();
        $arrival_city = City::where('name', 'Rome')->first();
        $date = Carbon::tomorrow();
        $search_form = $service->getOrCreateSearchForm($departure_city, $arrival_city, $date);
        $routes = $service->search($search_form);
        $this->assertInstanceOf(ResultRouteDto::class, $routes[0]);
    }
}
