<?php

namespace Tests\Unit;

use App\Models\City\City;
use App\Models\Route\Route;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Trip;
use App\Models\Way\Way;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TripsTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateTripWaysRoutesFromFactories() :void
    {
        $cities = City::factory(2)->create();
        $trip = Trip::factory()->create([
            'from_id' => $cities[0]->id,
            'to_id' => $cities[1]->id,
        ]);
        $this->assertInstanceOf(Trip::class, $trip);
        $ways = Way::factory(3)->create([
            'trip_id' => $trip->id
        ]);
        foreach ($ways as $way){
            $this->assertEquals($trip->id, $way->trip_id);
            $rand = rand(1,4);
            $routes = Route::factory($rand)->create([
                'way_id' => $way->id,
            ]);
            $this->assertEquals(count($routes), $rand);
            foreach ($routes as $route){
                $this->assertContains($route->type, RouteType::getTypes());
                $this->assertContains($route->transport_type, TransportType::getTypes());
            }
        }
    }
}
