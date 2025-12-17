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
        return [
            'business_id' => Business::factory(),
            'name' => fake()->name(),
            'specialization' => fake()->jobTitle(),
            'description' => fake()->optional()->text(200),
            'phone' => '+37529' . fake()->numerify('#######'),
            'email' => fake()->optional()->safeEmail(),
        ];
    }
}

