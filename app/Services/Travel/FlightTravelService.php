<?php
namespace App\Services\Travel;

use App\Models\Way\PartWay;

interface FlightTravelService
{
    public function getRoutes(PartWay $part_way) :array;

    public function search(PartWay $part_way) :void;

    public function getServiceName() :string;
}
