<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'interval' => $this->faker->randomElement(['monthly', 'yearly']),
            'trial_days' => 0,
            'is_active' => true,
            'is_default' => false,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Бесплатный тариф
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Бесплатный',
            'slug' => 'free',
            'price' => null,
            'trial_days' => 0,
            'is_default' => true,
            'sort_order' => 0,
        ]);
    }

    /**
     * Тариф с пробным периодом
     */
    public function withTrial(int $days = 7): static
    {
        return $this->state(fn (array $attributes) => [
            'trial_days' => $days,
        ]);
    }

    /**
     * Неактивный тариф
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Месячный тариф
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'interval' => 'monthly',
        ]);
    }

    /**
     * Годовой тариф
     */
    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'interval' => 'yearly',
        ]);
    }
}
