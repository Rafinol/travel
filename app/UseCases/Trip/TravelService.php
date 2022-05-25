<?php
namespace App\UseCases\Trip;

use App\Models\City\City;
use App\Models\Trip\Trip;
use App\UseCases\Trip\CreateTripService;
use Carbon\Carbon;

class TravelService
{
    private WayService $wayService;

    public function __construct(WayService $wayService)
    {
        $this->wayService = $wayService;
    }
    public function create(string $from, string $to, Carbon $date) :Trip
    {
        $departure = City::where('name', $from)->first();
        $arrival = City::where('name', $to)->first();
        $trip = $this->getOrCreateTrip($departure, $arrival, $date);
        if(!$trip->isCreated()){ //if already created and has any processing status
            return $trip;
        }
        $this->wayService->create($trip);
        $trip->changeStatusToWaiting();
        return $trip;
    }

    public function search(Trip $trip) :void
    {
        if($trip->isCompleted()){
            return;
        }
        $trip->changeStatusToSearching();
        foreach ($trip->ways as $way){
            $this->wayService->search($way);
        }
        $trip->changeStatusToCompleted();
    }

    private function getOrCreateTrip(City $departure_city, City $arrival_city, Carbon $date) :Trip
    {
        $trip = Trip::where(['departure_date' => $date, 'from_id' => $departure_city->id, 'to_id' => $arrival_city->id])->first();
        if(!$trip){
            $trip = Trip::new($departure_city->id, $arrival_city->id, $date->startOfDay());
        }
        return $trip;
    }

}
