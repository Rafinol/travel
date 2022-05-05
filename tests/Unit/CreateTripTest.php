<?php
namespace Tests\Unit;

use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\UseCases\Trip\TripService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTripTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateFromRandomCity()
    {
        City::factory(10)->create();
        $from_city = City::getRandomCity();
        $to_city = City::getRandomCityExceptFor($from_city->id);
        $this->assertInstanceOf(City::class, $from_city);
        $this->assertInstanceOf(City::class, $to_city);

        $trip_request = new TripRequest();
        $trip_request->from_id = $from_city->id;
        $trip_request->to_id = $to_city->id;
        var_dump($trip_request);
        $trip_service = \App::make(TripService::class);
        $trip_service->create($trip_request);
    }

}
