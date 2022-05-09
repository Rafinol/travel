<?php
namespace App\Services\Travel;

use App\Models\City\City;
use App\Models\Trip\Trip;
use App\Models\Way\Way;

interface TravelService
{
    public function getRoutes(Way $way) :array;

    public function getServiceName() :string;
}
