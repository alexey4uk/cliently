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
            ['from' => '09:00', 'to' => '18:00', 'day_off' => false],
            ['from' => '09:00', 'to' => '18:00', 'day_off' => false],
            ['from' => '09:00', 'to' => '18:00', 'day_off' => false],
            ['from' => '09:00', 'to' => '18:00', 'day_off' => false],
            ['from' => '09:00', 'to' => '18:00', 'day_off' => false],
            ['from' => null, 'to' => null, 'day_off' => true],
            ['from' => null, 'to' => null, 'day_off' => true],
        ];

        return [
            'business_id' => Business::factory(),
            'name' => fake()->streetName(),
            'address' => fake()->address(),
            'description' => fake()->text(200),
            'phone' => '+37529'.fake()->numerify('#######'),
            'email' => fake()->optional()->safeEmail(),
            'working_hours' => json_encode($workingHours),
        ];
    }
}
