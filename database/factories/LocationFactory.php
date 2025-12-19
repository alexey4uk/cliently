<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workingHours = [
            'from' => '09:00',
            'to' => '18:00',
            '24_hours' => false,
            'days_off' => [],
        ];

        return [
            'business_id' => Business::factory(),
            'name' => fake()->streetName(),
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'house' => fake()->buildingNumber(),
            'building' => fake()->optional()->buildingNumber(),
            'apartment' => fake()->optional()->buildingNumber(),
            'description' => fake()->text(200),
            'phone' => '+37529'.fake()->numerify('#######'),
            'working_hours' => json_encode($workingHours),
        ];
    }
}
