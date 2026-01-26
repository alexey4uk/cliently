<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = now();

        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => $now->copy()->addMonth(),
            'trial_ends_at' => null,
            'cancelled_at' => null,
            'metadata' => [],
            'invoice_id' => null,
            'payment_status' => 'paid',
        ];
    }

    /**
     * Пробная подписка
     */
    public function trial(): static
    {
        $now = now();

        return $this->state(fn(array $attributes) => [
            'status' => 'trial',
            'trial_ends_at' => $now->copy()->addDays(7),
            'ends_at' => null,
        ]);
    }

    /**
     * Истекшая подписка
     */
    public function expired(): static
    {
        $now = now();

        return $this->state(fn(array $attributes) => [
            'status' => 'active',
            'ends_at' => $now->copy()->subDay(),
        ]);
    }

    /**
     * Отмененная подписка
     */
    public function cancelled(): static
    {
        $now = now();

        return $this->state(fn(array $attributes) => [
            'status' => 'active',
            'cancelled_at' => $now,
            'ends_at' => $now->copy()->addMonth(),
        ]);
    }

    /**
     * Истекший триал
     */
    public function expiredTrial(): static
    {
        $now = now();

        return $this->state(fn(array $attributes) => [
            'status' => 'trial',
            'trial_ends_at' => $now->copy()->subDay(),
            'ends_at' => null,
        ]);
    }

    /**
     * Подписка с сохраненным предыдущим планом
     */
    public function withPreviousPlan(int $previousPlanId, string $previousPlanName): static
    {
        $now = now();

        return $this->state(fn(array $attributes) => [
            'metadata' => [
                'previous_plan_id' => $previousPlanId,
                'previous_plan_name' => $previousPlanName,
                'preserved_ends_at' => $now->copy()->addMonth()->toIso8601String(),
            ],
            'ends_at' => $now->copy()->addMonth(),
        ]);
    }

    /**
     * Подписка с использованными триалами
     */
    public function withUsedTrials(array $planIds): static
    {
        return $this->state(fn(array $attributes) => [
            'metadata' => [
                'used_trials' => $planIds,
            ],
        ]);
    }
}
