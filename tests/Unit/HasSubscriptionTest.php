<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_subscription_returns_subscription_when_active()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $activeSubscription = $user->activeSubscription();

        $this->assertNotNull($activeSubscription);
        $this->assertEquals('active', $activeSubscription->status);
    }

    public function test_active_subscription_returns_null_when_no_subscription()
    {
        $user = User::factory()->create();

        $activeSubscription = $user->activeSubscription();

        $this->assertNull($activeSubscription);
    }

    public function test_active_subscription_returns_null_when_subscription_expired()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()
            ->expired()
            ->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

        $activeSubscription = $user->activeSubscription();

        $this->assertNull($activeSubscription);
    }

    public function test_active_subscription_returns_trial_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()
            ->trial()
            ->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

        $activeSubscription = $user->activeSubscription();

        $this->assertNotNull($activeSubscription);
        $this->assertEquals('trial', $activeSubscription->status);
    }

    public function test_active_subscription_returns_null_when_trial_expired()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()
            ->expiredTrial()
            ->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

        $activeSubscription = $user->activeSubscription();

        $this->assertNull($activeSubscription);
    }

    public function test_has_active_subscription_returns_true_when_active()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($user->hasActiveSubscription());
    }

    public function test_has_active_subscription_returns_false_when_no_subscription()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->hasActiveSubscription());
    }

    public function test_get_current_plan_returns_plan_when_active_subscription_exists()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $currentPlan = $user->getCurrentPlan();

        $this->assertNotNull($currentPlan);
        $this->assertEquals($plan->id, $currentPlan->id);
    }

    public function test_get_current_plan_returns_null_when_no_active_subscription()
    {
        $user = User::factory()->create();

        $currentPlan = $user->getCurrentPlan();

        $this->assertNull($currentPlan);
    }
}
