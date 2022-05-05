<?php


namespace App\Models\City;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public static function getRandomCity() :self
    {
        return self::inRandomOrder()->first();
    }

    public static function getRandomCityExceptFor(int $except_id)
    {
        $point = self::inRandomOrder();
        if($except_id){
            $point->whereNotIn('id', [$except_id]);
        }
        return $point->first();
    }
}
