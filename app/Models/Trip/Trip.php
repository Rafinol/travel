<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Trip\Trip
 *
 * @property int $id
 * @property string $from
 * @property string $to
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
 */
class Trip extends Model
{
    use HasFactory;
}
