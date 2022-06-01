<?php

namespace App\Models\Route;

use App\Models\Point\Point;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Route\Route
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type moving, waiting
 * @property string $transport_type Air, bus, train
 * @property string $sdate
 * @property string $edate
 * @property int|null $price
 * @property int $duration seconds
 * @property string|null $from_id
 * @property string|null $to_id
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereEdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereSdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $way_id
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereWayId($value)
 * @property int $index
 * @method static \Database\Factories\Route\RouteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereTransportType($value)
 * @property int|null $part_way_id
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute wherePartWayId($value)
 * @property int $route_search_form_id
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereRouteSearchFormId($value)
 * @property int $route_id
 * @property-read \App\Models\Route\Route|null $route
 * @method static \Illuminate\Database\Eloquent\Builder|PartRoute whereRouteId($value)
 */
class PartRoute extends Model
{
    use HasFactory;

    protected $dates = ['sdate', 'edate'];

    public static function boot()
    {
        parent::boot();
        self::saving(function(self $model){
            $model->duration = $model->edate->timestamp-$model->sdate->timestamp;
        });
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function departure()
    {
        return $this->hasOne(Point::class, 'code', 'from_id');
    }

    public function arrival()
    {
        return $this->hasOne(Point::class, 'code', 'to_id');
    }
}
