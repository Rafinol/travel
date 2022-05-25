<?php
namespace App\UseCases\Trip\Way;

use App\Models\Route\RouteSearchForm;
use App\Models\Trip\Status;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WayStatus;

class LoadWaysService
{
    const TRANSFER_STOCK_TIME = 1; // hours

    public function load()
    {
        $this->updateDepartureDates(); // If previous part_way in table has arrival_time, update departure_time for next part_way
        $this->updateWayStatuses();
        $this->updateTripStatuses();
    }

    public function updateArrivalDates() :void
    {
        /*Select part_ways.id, rsf.id FROM part_ways
        JOIN route_search_form rsf on part_ways.route_search_form_id = rsf.id
        WHERE rsf.status = 'done' and part_ways.arrival_date is null*/
        $part_ways = PartWay::join('route_search_form rsf', 'rsf.id', '=', 'part_ways.route_search_form_id')
            ->where('rsf.status', RouteSearchForm::DONE_STATUS)
            ->whereNull('part_ways.arrival_date')
            ->get(['part_ways.id as id']);
        foreach ($part_ways as $part_way){
            $this->updateArrivalDate($part_way);
        }
    }

    public function updateArrivalDate(PartWay $part_way) :void
    {

    }

    public function updateDepartureDates() :void
    {
        \DB::table('part_ways pw')
            ->join('part_ways pw2', 'pw.position', '=', 'pw2.position+1')
            ->where('pw.way_id', 'pw2.way_id')
            ->whereNull('pw.departure_date')
            ->whereNotNull('pw2.arrival_date')
            ->update(['pw.departure_date' => '(pw2.arrival_date + INTERVAL '.self::TRANSFER_STOCK_TIME.' HOUR)']);
    }

    public function updateWayStatuses() :void
    {
        $not_ready_partway_ids = \DB::table('ways w')
            ->select('w.id')
            ->join('part_ways pw', 'w.id', '=', 'pw.way_id')
            ->whereNull('pw.arrival_date')
            ->where('status', '=', WayStatus::WAITING_STATUS)
            ->get('w.id'); // get way id with not ready partways
        Way::whereNotIn('id', $not_ready_partway_ids)
            ->where('status', '=', WayStatus::WAITING_STATUS)
            ->update(['status' => WayStatus::DONE_STATUS]);
    }

    public function updateTripStatuses() :void
    {
        \DB::raw("UPDATE trips t
            JOIN
                (SELECT trip_id FROM (
                    SELECT trip_id
                    FROM ways
                    GROUP BY trip_id, status) as w
                    GROUP BY trip_id
                    having count(*) = 1) as w ON t.id = w.trip_id
            SET status = '".Status::DONE_STATUS."'
            WHERE t.status = '".Status::WAITING_STATUS."'");
        /*$sub_query = \DB::table('ways w')
            ->select('trip_id')
            ->join('trips t', 't.id', '=', 'w.trip_id')
            ->where('t.status', Status::WAITING_STATUS)
            ->groupBy(['w.trip_id', 'w.status']);

        \DB::table($sub_query)
            ->select('trip_id')
            ->groupBy('trip_id')
            ->having('count(*)', 1);*/

    }
}
