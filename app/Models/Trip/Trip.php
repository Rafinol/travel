<?php

namespace App\Models\Trip;

use App\Models\City\City;
use App\Models\Way\Way;
use App\Models\Way\WayStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Trip\Trip
 *
 * @property int $id
 * @property int $from_id
 * @property int $to_id
 * @property string $departure_date
 * @property string $arrival_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Trip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trip query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereDepartureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $status
 * @property-read City|null $arrival
 * @property-read City|null $departure
 * @property-read \Illuminate\Database\Eloquent\Collection|Way[] $ways
 * @property-read int|null $ways_count
 * @method static \Database\Factories\Trip\TripFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereToId($value)
 */
class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['from_id', 'to_id', 'departure_date', 'status'];

    public static function new(int $from_id, int $to_id, $departure_date) :self
    {
        return self::create([
            'from_id' => $from_id,
            'to_id' => $to_id,
            'departure_date' => $departure_date,
            'status' => Status::NEW_STATUS,
        ]);
    }

    public function ways()
    {
        return $this->hasMany(Way::class);
    }

    public function departure()
    {
        return $this->hasOne(City::class, 'id', 'from_id');
    }

    public function arrival()
    {
        return $this->hasOne(City::class, 'id', 'to_id');
    }



    public function isCompleted()
    {
        if($this->status == Status::DONE_STATUS)
            return true;
        return false;
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
}
