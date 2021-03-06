<?php

namespace App\Models\Way;

use App\Models\City\City;
use App\Models\Route\PartRoute;
use App\Models\Route\RouteSearchForm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Way\PartWay
 *
 * @property int $id
 * @property int $way_id
 * @property int $position
 * @property Carbon $departure_date
 * @property Carbon $arrival_date
 * @property int $from_id
 * @property int $to_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read City|null $arrival
 * @property-read City|null $departure
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay query()
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereWayId($value)
 * @mixin \Eloquent
 * @property int $price
 * @property-read \Illuminate\Database\Eloquent\Collection|PartRoute[] $routes
 * @property-read int|null $routes_count
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay wherePrice($value)
 * @property int $route_search_form_id
 * @property-read RouteSearchForm|null $routeSearchForm
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereRouteSearchFormId($value)
 * @property int|null $min_price
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay whereMinPrice($value)
 */
class PartWay extends Model
{
    use HasFactory;

    protected $dates = ['departure_date', 'arrival_date'];

    public static function new($from_id, $to_id, $way_id, $position, $date) :self
    {
        $part_way = new self();
        $part_way->from_id = $from_id;
        $part_way->to_id = $to_id;
        $part_way->way_id = $way_id;
        $part_way->position = $position;
        if($position == 0){
            $part_way->departure_date = $date;
        }
        $part_way->save();
        return $part_way;
    }

    public function departure()
    {
        return $this->hasOne(City::class, 'id', 'from_id');
    }

    public function arrival()
    {
        return $this->hasOne(City::class, 'id', 'to_id');
    }

    public function routeSearchForm()
    {
        return $this->belongsTo(RouteSearchForm::class);
    }

    public function routes()
    {
        return $this->routeSearchForm->routes;
    }

    public function isCompleted() :bool //if part_way has an arrival date, that`s mean it is done
    {
        if($this->arrival_date)
            return true;
        return false;
    }



}
