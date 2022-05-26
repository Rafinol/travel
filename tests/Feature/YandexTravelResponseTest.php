<?php

namespace Tests\Feature;

use App\Models\City\City;
use App\Services\Travel\Yandex\YandexTravelService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class YandexTravelResponseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        /** @var YandexTravelService $service */
        $service = \App::make(YandexTravelService::class);
        $departure_city = City::where('name', 'Kazan')->first();
        $arrival_city = City::where('name', 'Rome')->first();
        $date = Carbon::tomorrow();
        $search_id = $service->createSearch($departure_city, $arrival_city, $date);
        sleep(2);
        $routes = $service->getResults($search_id);
        $this->assertArrayHasKey('reference', $routes);
        $this->assertArrayHasKey('variants', $routes);
        $this->assertArrayHasKey('fares', $routes['variants']);
        $this->assertArrayNotHasKey('sadfkkj', $routes);
    }
}
