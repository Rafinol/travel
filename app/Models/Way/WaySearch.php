<?php

namespace App\Models\Way;

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
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereSearchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WaySearch whereWayId($value)
 * @mixin \Eloquent
 */
class WaySearch extends Model
{
    use HasFactory;

    protected $table = 'ways_search';
    protected $fillable = ['way_id', 'type', 'status', 'search_id'];

    public static function new(int $way_id, string $type) :self
    {
        return self::create([
            'way_id' => $way_id,
            'type' => $type,
            'status' => WaySearchStatus::NEW_STATUS,
        ]);
    }

    public function isNew() :bool
    {
        if($this->status == WaySearchStatus::NEW_STATUS){
            return true;
        }
        return false;
    }

    public function isWaiting() :bool
    {
        if($this->status == WaySearchStatus::WAITING_STATUS && $this->created_at > now()->subSeconds(10)){
            return true;
        }
        return false;
    }

    public function isDone() :bool
    {
        if($this->status == WaySearchStatus::DONE_STATUS){
            return true;
        }
        if($this->created_at > now()->subSeconds(10)){ //TODO:: Выпилить отсюда
            return true;
        }
        return false;
    }

    public function changeStatusToWaiting()
    {
        $this->status = WaySearchStatus::WAITING_STATUS;
        $this->save();
    }

    public function changeStatusToDone()
    {
        $this->status = WaySearchStatus::DONE_STATUS;
        $this->save();
    }
}
