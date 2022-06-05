<?php
namespace App\Services\Travel\BusMock;

use App\Models\City\City;
use App\Dto\RouteDto\PointTypeDto;
use App\Dto\RouteDto\StationDto;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchForm;
use App\Dto\RouteDto\ResultRouteDto;
use App\Dto\RouteDto\RouteDto;
use App\Models\Transport\TransportType;
use App\Services\Travel\BusRideTravelService;
use App\Services\Travel\CommonTravelService;

class BusMockTravelService implements CommonTravelService, BusRideTravelService
{
    public function getRoutes(RouteSearch $route_search): array
    {
        $rides = [];
        $form = $route_search->searchForm;
        for ($i=0; $i<24; $i+=4) {
            $ride = new RouteDto();
            $ride->departure_date = $form->departure_date->addHour($i);
            $ride->arrival_date = $form->departure_date->addHours($i+10);
            $ride->departure_point = $this->getStation($form->departure);
            $ride->arrival_point = $this->getStation($form->arrival);
            $ride->transport_type = TransportType::BUS_TYPE;
            $ride->number = 'bus';
            $rides[] = new ResultRouteDto(
                3000,
                $ride->departure_date,
                $ride->arrival_date,
                [$ride]
            );
        }
        return $rides;
    }

    private function getStation(City $city) :StationDto
    {
        return new StationDto(
            str_replace(" ", '', $city->name),
            $city->name,
            PointTypeDto::BUS_TYPE
        );
    }

    public function search(RouteSearchForm $form): string
    {
        return $this->getServiceName().'-'.time();
    }

    public function getServiceName(): string
    {
        return 'bus_mock';
    }
}
