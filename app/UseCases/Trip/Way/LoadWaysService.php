<?php
namespace App\UseCases\Trip\Way;

use App\Models\Route\RouteSearchForm;
use App\Models\Trip\Status;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WayStatus;
use App\Repositories\Routes\RouteRepository;

class LoadWaysService
{
    const LAST_ARRIVAL_TIME = 20;
    private RouteRepository $repository;
    private PartWayService $partWayService;

    public function __construct(RouteRepository $repository, PartWayService $partWayService)
    {
        $this->repository = $repository;
        $this->partWayService = $partWayService;
    }

    public function load()
    {
        $this->updateArrivalDates();
        $this->updateDepartureDates(); // If previous part_way in table has arrival_time, update departure_time for next part_way
        $this->initSearchForm();
        $this->updateWayStatuses();
        $this->updateTripStatuses();
    }

    public function updateArrivalDates() :void
    {
        /*Select part_ways.id, rsf.id FROM part_ways
        JOIN route_search_form rsf on part_ways.route_search_form_id = rsf.id
        WHERE rsf.status = 'done' and part_ways.arrival_date is null*/
        $part_ways = PartWay::join('route_search_form', 'route_search_form.id', '=', 'part_ways.route_search_form_id')
            ->where('route_search_form.status', RouteSearchForm::DONE_STATUS)
            ->whereNull('part_ways.arrival_date')
            ->get(['part_ways.*']);
        foreach ($part_ways as $part_way){
            $this->updateArrivalDate($part_way);
        }
    }

    public function updateArrivalDate(PartWay $part_way) :void
    {
        $route = $this->repository->getCheapestRouteByPartWay($part_way);
        $part_way->arrival_date = $route->arrival_date;
        $part_way->save();
    }

    public function updateDepartureDates() :void
    {
        \DB::table('part_ways', 'pw')
            ->join('part_ways as pw2', 'pw.position', '=', \DB::raw('`pw2`.`position`+1'))
            ->where('pw.way_id', '=',\DB::raw('pw2.way_id'))
            ->whereNull('pw.departure_date')
            ->whereNotNull('pw2.arrival_date')
            ->update(['pw.departure_date' => \DB::raw(
                "IF(HOUR(pw2.arrival_date) < ".self::LAST_ARRIVAL_TIME.",
                    pw2.arrival_date,
                    DATE_FORMAT(DATE_ADD(pw2.arrival_date, INTERVAL 1 DAY),'%Y-%m-%d 00:00:00'))")]);
        //If the arrival is before 20:00, then we will take the this day
        //If the arrival is after 20:00, then we will search for tickets the next day
    }

    public function initSearchForm()
    {
        $part_ways = PartWay::whereNotNull('departure_date')->whereNull('route_search_form_id')->get();
        foreach ($part_ways as $part_way){
            $this->partWayService->prepareForSearch($part_way);
        }
    }

    public function updateWayStatuses() :void
    {
        $not_ready_partway_ids = \DB::table('ways')
            ->select('ways.id')
            ->join('part_ways', 'ways.id', '=', 'part_ways.way_id')
            ->whereNull('part_ways.arrival_date')
            ->where('status', '=', WayStatus::WAITING_STATUS)
            ->groupBy('ways.id')
            ->pluck('ways.id')->toArray(); // get way id with not ready partways
        Way::whereNotIn('id', $not_ready_partway_ids)
            ->where('status', '=', WayStatus::WAITING_STATUS)
            ->update(['status' => WayStatus::DONE_STATUS]);
    }

    public function updateTripStatuses() :void
    {
        \DB::unprepared("UPDATE trips t
                                    JOIN (
                                        SELECT ways.trip_id, done_count, count(*) as all_count FROM ways
                                        JOIN (SELECT trip_id, count(*) as done_count FROM ways
                                              WHERE status = '".Status::DONE_STATUS."'
                                              GROUP BY trip_id, status) as done_ways ON done_ways.trip_id = ways.trip_id
                                        GROUP BY ways.trip_id
                                    ) as counts ON t.id = counts.trip_id
                                SET t.status = '".Status::TEMP_STATUS."'
                                WHERE t.status = '".Status::SEARCHING_STATUS."' and counts.done_count = counts.all_count");
    }
}
