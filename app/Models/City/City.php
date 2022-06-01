<?php


namespace App\Models\City;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\City\City
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $yandex_id
 * @method static \Database\Factories\City\CityFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereYandexId($value)
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;

    public static function getRandomCity() :City
    {
        return self::inRandomOrder()->first();
    }

    public static function getRandomCityExceptFor(int $except_id) :City
    {
        $point = self::inRandomOrder();
        if($except_id){
            $point->whereNotIn('id', [$except_id]);
        }
        return $point->first();
    }

    public function updateForeignId(string $sid) :self
    {
        $this->yandex_id = $sid;
        $this->save();
        return $this;
    }
}
