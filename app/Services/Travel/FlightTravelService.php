<?php
namespace App\Services\Travel;

use App\Models\Route\RouteSearchForm;
use App\Models\Route\RouteSearch;

interface FlightTravelService
{
    public function getRoutes(RouteSearch $way_search) :array;

    public function search(RouteSearchForm $route_search) :string;

    public function getServiceName() :string;
}
