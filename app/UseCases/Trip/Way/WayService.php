<?php
namespace App\UseCases\Trip\Way;

use App\Helpers\ArrayHelper;
use App\Models\Trip\Trip;
use App\Models\Way\Way;

class WayService
{
    private TravelWaysService $travel_ways;
    private PartWayService $partWayService;

    public function __construct(TravelWaysService $travel_ways, PartWayService $partWayService)
    {
        $this->travel_ways = $travel_ways;
        $this->partWayService = $partWayService;
    }

    public function create(Trip $trip)
    {
        $travel_ways = $this->travel_ways->getTravelWays($trip->departure->name, $trip->arrival->name);
        foreach ($travel_ways as $travel_way){
            $way = Way::new($trip->id, implode(' - ',$travel_way));
            foreach (ArrayHelper::splitOfPairs($travel_way) as $key => $cities){
                $this->partWayService->create($cities[0], $cities[1], $way, $key, $trip->departure_date);
            }
        }
    }

    public function search(Way $way)
    {
        if($way->isCompleted()){
            return;
        }
        $way->changeStatusToWaiting();
        foreach ($way->partWays as $part_way){
            $this->partWayService->search($part_way);
        }
        $way->changeStatusToCompleted();
    }
}
