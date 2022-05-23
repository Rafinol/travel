<?php
namespace App\UseCases\Trip\Departure;

use App\Http\Request\Trip\TripRequest;
use App\Models\Trip\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface DepartureService
{
    public function getTrip(string $from, string $to, Carbon $date) :Trip;

    public function search(Trip $trip) :void;
}
