<?php


namespace App\Http\Request\Trip;


use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules() :array
    {
        return [
            'from_id' => 'required|integer',
            'to_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d'
        ];
    }


}
