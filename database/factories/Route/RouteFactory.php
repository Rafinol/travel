<?php

namespace Database\Factories\Route;

use App\Dto\RouteDto\Point;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Way\Way;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $types = RouteType::getTypes();
        $transport_types = TransportType::getTypes();
        return [
            'type' => $types[$this->faker->numberBetween(0, max($types))],
            'transport_type' => $transport_types[$this->faker->numberBetween(0, max($transport_types))],
            'sdate' => $sdate = $this->faker->dateTimeBetween('now', '+1 month'),
            'edate' => $this->faker->dateTimeInInterval($sdate, '+1 day'),
        ];
    }
}
