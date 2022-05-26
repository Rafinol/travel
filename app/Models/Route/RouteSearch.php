<?php

namespace App\Models\Route;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Way\WaySearch
 *
 * @property int $id
 * @property int $way_id
 * @property string|null $search_id
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereSearchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereWayId($value)
 * @mixin \Eloquent
 * @property int $part_way_id
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch wherePartWayId($value)
 * @property string $departure_date
 * @property int $from_id
 * @property int $to_id
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereToId($value)
 * @property int $route_search_form_id
 * @method static \Illuminate\Database\Eloquent\Builder|RouteSearch whereRouteSearchFormId($value)
 */
class RouteSearch extends Model
{
    use HasFactory;

    protected $table = 'routes_search';
    protected $fillable = ['route_search_form_id', 'type', 'status', 'search_id'];

    public static function new(int $route_search_id, string $type) :self
    {
        return self::create([
            'route_search_form_id' => $route_search_id,
            'type' => $type,
            'status' => RouteSearchStatus::NEW_STATUS,
        ]);
    }

    public function isNew() :bool
    {
        if($this->status == RouteSearchStatus::NEW_STATUS){
            return true;
        }
        return false;
    }

    public function isWaiting() :bool
    {
        if($this->status == RouteSearchStatus::WAITING_STATUS){
            return true;
        }
        return false;
    }

    public function isCompleted() :bool
    {
        if($this->status == RouteSearchStatus::DONE_STATUS){
            return true;
        }
        return false;
    }

    public function changeStatusToWaiting()
    {
        $this->status = RouteSearchStatus::WAITING_STATUS;
        $this->save();
    }

    public function changeStatusToDone()
    {
        $this->status = RouteSearchStatus::DONE_STATUS;
        $this->save();
    }

    public function searchForm()
    {
        return $this->hasOne(RouteSearchForm::class);
    }
}
