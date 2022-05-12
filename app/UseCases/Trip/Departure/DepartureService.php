<?php
namespace App\UseCases\Trip\Departure;

use App\Http\Request\Trip\TripRequest;
use App\Models\Trip\Trip;
use Illuminate\Database\Eloquent\Collection;

interface DepartureService
{
    public function getTrip(TripRequest $request) :Trip;
}
