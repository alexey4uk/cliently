<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanFeature>
 */
class PlanFeatureFactory extends Factory
{
    protected $model = PlanFeature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'feature_key' => $this->faker->word(),
            'feature_value' => $this->faker->numberBetween(1, 100),
            'feature_type' => 'integer',
        ];
    }

    /**
     * Boolean feature
     */
    public function boolean(bool $value = true): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_type' => 'boolean',
            'feature_value' => $value ? 'true' : 'false',
        ]);
    }

    /**
     * Integer feature
     */
    public function integer(int $value = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_type' => 'integer',
            'feature_value' => (string) $value,
        ]);
    }

    /**
     * Unlimited feature (-1)
     */
    public function unlimited(): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_type' => 'integer',
            'feature_value' => '-1',
        ]);
    }
}
