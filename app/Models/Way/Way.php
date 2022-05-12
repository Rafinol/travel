<?php

namespace App\Models\Way;

use App\Models\Trip\Status;
use App\Models\Trip\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Way\Way
 *
 * @property int $id
 * @property int $trip_id
 * @property string $name
 * @property string $status
 * @property int|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Way newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Way newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Way query()
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereTripId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Way\PartWay[] $part_way
 * @property-read int|null $part_way_count
 * @property-read Trip $trip
 * @method static \Database\Factories\Way\WayFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Way whereStatus($value)
 */
class Way extends Model
{
    use HasFactory;

    protected $fillable = ['trip_id', 'name', 'status', ];

    public static function new(int $trip_id, string $name) :self
    {
        return self::create([
            'trip_id' => $trip_id,
            'name' => $name,
            'status' => WayStatus::NEW_STATUS
        ]);
    }

    public function isCreated()
    {
        if($this->status == WayStatus::NEW_STATUS){
            return true;
        }
        return false;
    }

    public function isWaiting()
    {
        if($this->status == WayStatus::WAITING_STATUS){
            return true;
        }
        return false;
    }

    public function isCompleted()
    {
        if($this->status == WayStatus::DONE_STATUS){
            return true;
        }
        return false;
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function part_way()
    {
        return $this->hasMany(PartWay::class)->orderBy('position');
    }

    public function changeStatusToWaiting()
    {
        $this->status = WayStatus::WAITING_STATUS;
        $this->save();
    }

    public function changeStatusToCompleted()
    {
        $this->status = WayStatus::DONE_STATUS;
        $this->save();
    }

    public static function firstOrNew(int $trip_id, string $name)
    {
        $way = self::where(['trip_id' => $trip_id, 'name' => $name])->first();
        if(!$way){
            $way = self::new($trip_id, $name);
        }
        return $way;
    }
}
