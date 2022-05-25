<?php

namespace App\Models\Route;

use App\Models\City\City;
use App\Models\Way\Way;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Route\RouteSearchForm
 *
 * @property int $id
 * @property Carbon $departure_date
 * @property int $from_id
 * @property int $to_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read City|null $arrival
 * @property-read City|null $departure
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Route\PartRoute[] $routes
 * @property-read int|null $routes_count
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm query()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearchForm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RouteSearchForm extends Model
{
    use HasFactory;

    protected $table = 'route_search_form';

    protected $fillable = ['from_id', 'to_id', 'departure_date'];
    protected $dates = ['departure_date'];

    const WAITING_STATUS = 'waiting';
    const DONE_STATUS = 'done';

    /*public static function new($from_id, $to_id, $departure_date) :self
    {
        $part_way = new self();
        $part_way->from_id = $from_id;
        $part_way->to_id = $to_id;
        $part_way->departure_date = $departure_date;
        $part_way->save();
        return $part_way;
    }*/

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

    public function scopeWaiting(Builder $query)
    {
        return $query->where('status', self::WAITING_STATUS);
    }
}
