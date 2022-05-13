<?php


namespace App\UseCases\Trip\Type;


use App\Models\Way\PartWay;
use App\Models\Way\WaySearch;
use App\Services\Travel\FlightTravelService;

trait TripTrait
{
    public function search(PartWay $part_way) :void
    {
        $way_search = WaySearch::where(['type' => $this->service->getServiceName(), 'way_id' => $part_way->id,])->where('created_at', '>', now()->subDay())->first();
        if(!$way_search) {
            $way_search = WaySearch::new($part_way->id, $this->service->getServiceName());
        }
        if($way_search->isDone()){
            return;
        }
        $search_id = $this->service->search($part_way);
        $way_search->update(['search_id'=> $search_id]);
    }

    public function getRoutes(PartWay $part_way) :array
    {
        $way_search = WaySearch::where(['type' => $this->service->getServiceName(), 'part_way_id' => $part_way->id,])->first();
        return $this->service->getRoutes($way_search);
    }

}
