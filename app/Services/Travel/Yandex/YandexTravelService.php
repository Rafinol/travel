<?php


namespace App\Services\Travel\Yandex;


use App\Exceptions\CityNotFoundException;
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
use App\Services\Travel\CommonTravelService;
use App\Services\Travel\FlightTravelService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;

class YandexTravelService implements FlightTravelService, CommonTravelService
{
    const INIT_URL = 'https://travel.yandex.ru/api/avia/search/init';
    const GET_CITY_URL = 'https://travel.yandex.ru/api/avia/searchSuggest';
    const GET_RESULT_URL = 'https://travel.yandex.ru/api/avia/search/results';

    const SERVICE_NAME = 'yandex_travel';

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
            //'proxy' => 'https://PAJWTR:5XYTLV@217.29.63.254:12021'
        ];
        $result = Http::withHeaders(['proxy' => 'https://PAJWTR:5XYTLV@217.29.63.254:12021'])->get(self::INIT_URL, $body);
        /*if(!$result->json()){
            sleep(90);
            return $this->createSearch($from, $to, $date);
        }*/
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
        /*if(!$response){
            sleep(90);
            return $this->getRoutes($route_search);
        }*/
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
