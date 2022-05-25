<?php

namespace Tests\Unit;

use App\Models\City\City;
use App\UseCases\Trip\TravelWaysService;
use Tests\TestCase;

class IssetCitiesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        /** @var TravelWaysService $service */
        $service = \App::make(TravelWaysService::class);
        $cities = $service->getUniqueCitiesFromWays();
        $city_count_from_storage = City::whereIn('name' , $cities)->count();
        $this->assertEquals(count($cities), $city_count_from_storage);
    }
}
