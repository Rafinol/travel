<?php

namespace App\Models\Way;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Way\Way
 *
 * @property int $id
 * @property int $trip_id
 * @property string $name
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
 */
class Way extends Model
{
    use HasFactory;
}
