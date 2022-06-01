<?php

namespace Database\Factories\Trip;

use App\Models\Trip\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'departure_date' => $this->faker->dateTimeBetween('now()', '+1 month'),
            'status' => Status::NEW_STATUS,
        ];
    }
}
