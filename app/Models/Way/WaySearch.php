<?php

namespace App\Models\Way;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaySearch extends Model
{
    use HasFactory;

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
        if($this->status == WaySearchStatus::WAITING_STATUS){
            return true;
        }
        return false;
    }

    public function isDone() :bool
    {
        if($this->status == WaySearchStatus::DONE_STATUS){
            return true;
        }
        if($this->created_at > now()->addSeconds(30)){ //TODO:: Выпилить отсюда
            return true;
        }
        return false;
    }
}
