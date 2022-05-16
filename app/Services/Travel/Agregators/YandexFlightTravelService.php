<?php


namespace App\Services\Travel\Agregators;


use App\Exceptions\RoutesAlreadyDoneException;
use App\Exceptions\RoutesNotReadyYetException;
use App\Models\City\City;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchForm;
use App\Models\RouteDto\RouteDto;
use App\Models\RouteDto\ResultRouteDto;
use App\Models\Point\StationDto;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Services\Travel\FlightTravelService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class YandexFlightTravelService implements FlightTravelService
{
    const INIT_URL = 'https://travel.yandex.ru/api/avia/search/init';
    const GET_CITY_URL = 'https://travel.yandex.ru/api/avia/searchSuggest';
    const GET_RESULT_URL = 'https://travel.yandex.ru/api/avia/search/results';

    const SERVICE_NAME = 'yandex_travel';

    public function getServiceName() :string
    {
        return self::SERVICE_NAME;
    }

    public function search(RouteSearchForm $route_search_form) :string
    {
        return $this->createSearch($route_search_form->departure, $route_search_form->arrival, $route_search_form->departure_date);
    }

    private function createSearch(City $from, City $to, $date) :string
    {
        if(!$from->yandex_id || !$to->yandex_id){
            $from = $from->updateForeignId($this->getCityId($from->name));
            $to = $to->updateForeignId($this->getCityId($to->name));
        }
        $body = [
            'fromId' => $from->yandex_id,
            'toId' => $to->yandex_id,
            'when' => $date->format('Y-m-d'),
            'adult_seats' => 1,
            'children_seats' => 0,
            'infant_seats' => 0,
            'klass' => 'economy',
            'oneway' => 1,
        ];
        $result = Http::get(self::INIT_URL, $body);
        return $result['id'];
    }

    private function getCityId(string $city_name) :string
    {
        $result = Http::get(self::GET_CITY_URL, [
            'field' => 'from',
            'query' => $city_name,
            'otherPoint' => 'c11475',
            'clientCity' => 43,
            'showCountries' => 0,
            'showAnywhere' => 0,
        ]);
        $json = $result->json();
        return $result['items'][0]['pointKey'];
    }

    public function getRoutes(RouteSearch $route_search): array
    {
        return $this->getResults($route_search->search_id);
    }

    private function getResults(string $search_id) :array
    {
        $params = [
            'qid' => $search_id,
            'cont' => 0,
            'allowPortional' => 1,
        ];
        $response = Http::retry(3, 10000)->get(self::GET_RESULT_URL, $params);
        if(!$response){
            throw new \DomainException('travels not found:(');
        }
        $ya_flights = new YandexFlight($response->json());
        return $ya_flights->getFlights();
    }
}
