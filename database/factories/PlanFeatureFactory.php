<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\SubscriptionMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanFeature>
 */
class PlanFeatureFactory extends Factory
{
    protected $model = PlanFeature::class;

    /**
     * Define the model's default state.
     * Table plan_features: plan_id, metric_id, value.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'metric_id' => SubscriptionMetric::factory(),
            'value' => (string) $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Boolean feature (value 'true' / 'false')
     */
    public function boolean(bool $value = true): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $value ? 'true' : 'false',
        ]);
    }

    /**
     * Integer feature
     */
    public function integer(int $value = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => (string) $value,
        ]);
    }

    /**
     * Unlimited feature (-1)
     */
    public function unlimited(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => '-1',
        ]);
    }

    /**
     * Use metric by key (creates metric if needed). Use in tests: forMetricKey('max_locations')
     */
    public function forMetricKey(string $key, string $type = 'integer'): static
    {
        return $this->state(function (array $attributes) use ($key, $type) {
            $metric = SubscriptionMetric::firstOrCreate(
                ['key' => $key],
                ['label' => $key, 'type' => $type, 'is_active' => true, 'sort_order' => 0]
            );

            return ['metric_id' => $metric->id];
        });
    }
}
