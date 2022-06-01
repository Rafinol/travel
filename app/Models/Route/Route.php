<?php

namespace App\Models\Route;

use App\Models\Route\PartRoute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Route\Route
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $departure_date
 * @property \Illuminate\Support\Carbon $arrival_date
 * @property int $price
 * @property int $transfers_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $route_search_form_id
 * @property-read \Illuminate\Database\Eloquent\Collection|PartRoute[] $route
 * @property-read int|null $route_count
 * @method static \Database\Factories\Route\RouteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereRouteSearchFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|PartRoute[] $partRoutes
 * @property-read int|null $part_routes_count
 */
class Route extends Model
{
    use HasFactory;

    protected $dates = ['departure_date', 'arrival_date'];

    public function partRoutes()
    {
        return $this->hasMany(PartRoute::class);
    }

    public static function new(Carbon $departure_date, Carbon $arrival_date, int $price, int $route_search_form_id, int $transfers_count) :self
    {
        $self = new self();
        $self->departure_date = $departure_date;
        $self->arrival_date = $arrival_date;
        $self->route_search_form_id = $route_search_form_id;
        $self->price = $price;
        $self->transfers_count = $transfers_count;
        $self->save();
        return $self;

    }
}
