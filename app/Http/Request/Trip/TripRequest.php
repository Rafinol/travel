<?php


namespace App\Http\Request\Trip;


use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    public function rules() :array
    {
        return [
            'from_id' => 'required|number',
            'to_id' => 'required|number',
            'date' => 'required|date'
        ];
    }
}
