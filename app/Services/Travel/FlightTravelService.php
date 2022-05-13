<?php
namespace App\Services\Travel;

use App\Models\Way\PartWay;
use App\Models\Way\WaySearch;

interface FlightTravelService
{
    public function getRoutes(WaySearch $way_search) :array;

    public function search(PartWay $part_way) :string;

    public function getServiceName() :string;
}
