<?php


namespace App\Helpers;


class ArrayHelper
{
    public static function splitOfPairs(array $array) :array
    {
        if(count($array) < 2)
            return $array;
        $result = [];
        foreach ($array as $i => $item){
            if($i < count($array)-1){
                $result[] = [$item, $array[$i+1]];
            }
        }
        return $result;
    }
}
