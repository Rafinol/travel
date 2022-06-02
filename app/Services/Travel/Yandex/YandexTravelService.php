<?php


namespace App\Services\Travel\Yandex;


use App\Exceptions\CityNotFoundException;
use App\Models\City\City;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchForm;
use App\Services\Travel\CommonTravelService;
use App\Services\Travel\FlightTravelService;
use Illuminate\Support\Facades\Http;

class YandexTravelService implements FlightTravelService, CommonTravelService
{
    const INIT_URL = 'https://travel.yandex.ru/api/avia/search/init';
    const GET_CITY_URL = 'https://travel.yandex.ru/api/avia/searchSuggest';
    const GET_RESULT_URL = 'https://travel.yandex.ru/api/avia/search/results';

    const SERVICE_NAME = 'yandex_travel';

    private Http $http;

    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    public function getServiceName() :string
    {
        return self::SERVICE_NAME;
    }

    public function search(RouteSearchForm $form) :string
    {
        return $this->createSearch($form->departure, $form->arrival, $form->departure_date);
    }

    public function createSearch(City $from, City $to, $date) :string
    {
        if(!$from->yandex_id){
            $from = $from->updateForeignId($this->getCityId($from->name));
        }
        if(!$to->yandex_id){
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
        $result = $this->http::get(self::INIT_URL, $body);
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
        if(empty($result['items'])){
            throw new CityNotFoundException();
        }
        return $result['items'][0]['pointKey'];
    }

    public function getRoutes(RouteSearch $route_search): array
    {
        $response = $this->getResults($route_search->search_id);
        $ya_flights = new YandexFlight($response);
        return $ya_flights->getFlights();
    }

    public function getResults(string $search_id) : array
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
        return $response->json();
    }
}
