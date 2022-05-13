<?php

namespace App\Models\Way;

use App\Models\City\City;
use App\Models\Route\Route;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Route[] $routes
 * @property-read int|null $routes_count
 * @method static \Illuminate\Database\Eloquent\Builder|PartWay wherePrice($value)
 */
class PartWay extends Model
{
    use HasFactory;

    protected $dates = ['departure_date', 'arrival_date'];

    public static function new($from_id, $to_id, $way_id, $position) :self
    {
        $part_way = self();
        $part_way->from_id = $from_id;
        $part_way->to_id = $to_id;
        $part_way->way_id = $way_id;
        $part_way->position = $position;
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

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
