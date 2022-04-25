<?php

namespace App\Models\Route;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Route\Route
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type Air, bus, train, waiting
 * @property string $sdate
 * @property string $edate
 * @property int|null $price
 * @property int $duration seconds
 * @property string|null $from_id
 * @property string|null $to_id
 * @method static \Illuminate\Database\Eloquent\Builder|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereEdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereSdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $way_id
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereWayId($value)
 */
class Route extends Model
{
    use HasFactory;
}
