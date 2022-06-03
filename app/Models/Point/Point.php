<?php

namespace App\Dto\RouteDto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Point\Point
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point query()
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $city_id
 * @method static \Database\Factories\Point\PointFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereCityId($value)
 */
class Point extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'type', 'address', 'latitude', 'longitude'];
}
