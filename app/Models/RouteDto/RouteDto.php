<?php
namespace App\Models\RouteDto;

use App\Models\Point\Point;
use App\Models\Point\StationDto;
use Carbon\Carbon;

class RouteDto
{
    public string $number;
    public Carbon $departure_date;
    public Carbon $arrival_date;
    public StationDto $departure_point;
    public StationDto $arrival_point;
}
