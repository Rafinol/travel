<?php
namespace App\UseCases\Trip\Way;

use App\Models\City\City;

class TravelWaysService
{
    public array $ways;

    public function __construct(array $ways)
    {
        $this->ways = $ways;
    }

    public function getTravelWays(string $departure, string $arrival) :array
    {
        $ways = $this->getAddedCityEnds($departure, $arrival);
        return $this->changeCitiesNameToModels($ways);
    }

    private function getCityModels() :array
    {
        $unique_cities = $this->getUniqueCitiesFromWays();
        $cities = City::where('name', $unique_cities)->get();
        return collect($cities)->keyBy('name')->all();
    }

    public function getUniqueCitiesFromWays() :array
    {
        $cities = [];
        foreach ($this->ways as $route){
            foreach($route as $city){
                $cities[] = $city;
            }
        }
        return array_unique($cities);
    }

    private function changeCitiesNameToModels(array $ways) :array
    {
        $cities = $this->getCityModels();
        foreach ($ways as $key => $way){
            foreach ($way as $part_key => $city_name){
                $ways[$key][$part_key] = $cities[$city_name];
            }
        }
        return $ways;
    }

    private function getAddedCityEnds(string $departure, string $arrival) :array
    {
        $ways = array_map(function($way) use ($departure, $arrival){
            array_unshift($way, $departure);
            if(last($way) != $arrival) {
                array_push($way, $arrival);
            }
            return $way; // Add the beginning and end of the route to each element of the array
        }, $this->ways);
        array_unshift($ways, [$departure, $arrival]);
        return $ways;
    }

}
