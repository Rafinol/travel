<?php

namespace Database\Factories\Point;

use App\Dto\RouteDto\PointType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $point_types = PointType::getTypes();
        return [
            'name' => $this->faker->city,
            'type' => $point_types[$this->faker->numberBetween(0, max($point_types))],
            'code' => $this->faker->countryCode,
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
