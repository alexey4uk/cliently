<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Master;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Master>
 */
class MasterFactory extends Factory
{
    protected $model = Master::class;

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
            'user_id' => \App\Models\User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'specialization' => fake()->jobTitle(),
            'description' => fake()->optional()->text(200),
            'phone' => '+37529'.fake()->numerify('#######'),
            'email' => fake()->optional()->safeEmail(),
            'working_hours' => json_encode($workingHours),
        ];
    }
}
