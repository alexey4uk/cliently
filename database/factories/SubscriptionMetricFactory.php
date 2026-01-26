<?php

namespace Database\Factories;

use App\Models\SubscriptionMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionMetric>
 */
class SubscriptionMetricFactory extends Factory
{
    protected $model = SubscriptionMetric::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'label' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'icon' => 'fa-circle',
            'type' => 'integer',
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Неактивная метрика
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
