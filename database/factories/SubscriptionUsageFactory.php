<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionUsage>
 */
class SubscriptionUsageFactory extends Factory
{
    protected $model = SubscriptionUsage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'user_id' => User::factory(),
            'feature_key' => 'max_appointments_per_month',
            'current_usage' => 0,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ];
    }
}
