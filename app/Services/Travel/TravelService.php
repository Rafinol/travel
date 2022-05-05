<?php
namespace App\Services\Travel;

use App\Models\City\City;

interface TravelService
{
    public function getWays(City $from, City $to, $date) :array;
}
