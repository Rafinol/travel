<?php
namespace App\Models\Flight;

use App\Models\Point\Point;
use App\Models\Point\Station;
use Carbon\Carbon;

class Flight
{
    public string $flight_number;
    public Carbon $departure_date;
    public Carbon $arrival_date;
    public Station $departure_point;
    public Station $arrival_point;
}
