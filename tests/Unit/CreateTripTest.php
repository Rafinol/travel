<?php
namespace Tests\Unit;

use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\UseCases\Trip\Departure\DefaultTripService;
use App\UseCases\Trip\Departure\KazanTripService;
use App\UseCases\Trip\TripService;
use Carbon\Carbon;
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
        $trip_request['from_id'] = $from_city->id;
        $trip_request['to_id'] = $to_city->id;
        $trip_request['date'] = new Carbon();

        $trip_service = \App::make(TripService::class);
        $trip_service->create($trip_request);
        $service = TripService::getService($from_city->name);
        if($from_city->name == 'Kazan'){
            $this->assertInstanceOf(KazanTripService::class, $service);
        }
        else{
            $this->assertInstanceOf(DefaultTripService::class, $service);
        }
    }

}
