<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_is_active_when_status_is_active_and_ends_at_in_future()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function test_subscription_is_not_active_when_status_is_not_active()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertFalse($subscription->isActive());
    }

    public function test_subscription_is_not_active_when_ends_at_in_past()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($subscription->isActive());
    }

    public function test_subscription_is_active_when_ends_at_is_null()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => null,
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function test_subscription_is_trial_when_status_is_trial_and_trial_ends_at_in_future()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->trial()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertTrue($subscription->isTrial());
    }

    public function test_subscription_is_not_trial_when_trial_ends_at_in_past()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->expiredTrial()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertFalse($subscription->isTrial());
    }

    public function test_subscription_is_cancelled_when_cancelled_at_is_set()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->cancelled()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $this->assertTrue($subscription->isCancelled());
    }

    public function test_subscription_will_cancel_at_end_when_cancelled_and_active()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->cancelled()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($subscription->willCancelAtEnd());
    }

    public function test_subscription_will_not_cancel_at_end_when_not_cancelled()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertFalse($subscription->willCancelAtEnd());
    }

    public function test_get_effective_plan_returns_current_plan_when_no_previous_plan()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $effectivePlan = $subscription->getEffectivePlan();

        $this->assertEquals($plan->id, $effectivePlan->id);
    }

    public function test_get_effective_plan_returns_previous_plan_when_ends_at_in_future()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->free()->create();
        $previousPlan = Plan::factory()->create();

        $subscription = Subscription::factory()->withPreviousPlan($previousPlan->id, $previousPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $currentPlan->id,
            'ends_at' => now()->addMonth(),
        ]);

        $effectivePlan = $subscription->getEffectivePlan();

        $this->assertEquals($previousPlan->id, $effectivePlan->id);
    }

    public function test_get_effective_plan_returns_current_plan_when_ends_at_expired()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->free()->create();
        $previousPlan = Plan::factory()->create();

        $subscription = Subscription::factory()->withPreviousPlan($previousPlan->id, $previousPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $currentPlan->id,
            'ends_at' => now()->subDay(),
        ]);

        $effectivePlan = $subscription->getEffectivePlan();

        $this->assertEquals($currentPlan->id, $effectivePlan->id);
    }

    public function test_can_use_feature_uses_effective_plan()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->free()->create();
        $previousPlan = Plan::factory()->create();

        $metric = \App\Models\SubscriptionMetric::firstOrCreate(
            ['key' => 'analytics_enabled'],
            ['label' => 'Analytics', 'type' => 'boolean', 'is_active' => true, 'sort_order' => 0]
        );
        PlanFeature::factory()->boolean(true)->create([
            'plan_id' => $previousPlan->id,
            'metric_id' => $metric->id,
            'value' => 'true',
        ]);

        $subscription = Subscription::factory()->withPreviousPlan($previousPlan->id, $previousPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $currentPlan->id,
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($subscription->canUseFeature('analytics_enabled'));
    }

    public function test_get_feature_limit_uses_effective_plan()
    {
        $user = User::factory()->create();
        $currentPlan = Plan::factory()->free()->create();
        $previousPlan = Plan::factory()->create();

        $metric = \App\Models\SubscriptionMetric::firstOrCreate(
            ['key' => 'max_locations'],
            ['label' => 'Locations', 'type' => 'integer', 'is_active' => true, 'sort_order' => 0]
        );
        PlanFeature::factory()->integer(50)->create([
            'plan_id' => $previousPlan->id,
            'metric_id' => $metric->id,
            'value' => '50',
        ]);

        $subscription = Subscription::factory()->withPreviousPlan($previousPlan->id, $previousPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $currentPlan->id,
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertEquals(50, $subscription->getFeatureLimit('max_locations'));
    }
}
