<?php
namespace App\Dto\RouteDto;

use App\Dto\RouteDto\Point;
use App\Dto\RouteDto\StationDto;
use Carbon\Carbon;

class RouteDto
{
    public string $number;
    public Carbon $departure_date;
    public Carbon $arrival_date;
    public StationDto $departure_point;
    public StationDto $arrival_point;
    public string $transport_type;
}
