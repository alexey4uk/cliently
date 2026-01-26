<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'subscription_id' => null,
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'currency' => 'BYN',
            'status' => 'pending',
            'bepaid_transaction_id' => null,
            'bepaid_payment_token' => $this->faker->uuid(),
            'payment_method' => 'redirect',
            'paid_at' => null,
            'expires_at' => now()->addDays(7),
            'metadata' => [],
        ];
    }

    /**
     * Оплаченный инвойс
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
            'bepaid_transaction_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Неудачный платеж
     */
    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }

    /**
     * Инвойс для продления
     */
    public function renewal(): static
    {
        return $this->state(fn(array $attributes) => [
            'metadata' => [
                'is_renewal' => true,
            ],
        ]);
    }

    /**
     * Инвойс для смены тарифа
     */
    public function planChange(int $oldPlanId, string $oldPlanName): static
    {
        return $this->state(fn(array $attributes) => [
            'metadata' => [
                'is_plan_change' => true,
                'old_plan_id' => $oldPlanId,
                'old_plan_name' => $oldPlanName,
                'preserve_ends_at' => true,
            ],
        ]);
    }
}
