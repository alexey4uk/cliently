<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Subscription;
use App\Models\SubscriptionMetric;
use App\Models\SubscriptionUsage;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SubscriptionService::class);
    }

    public function test_create_subscription_creates_new_subscription_for_user_without_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $subscription = $this->service->createSubscription($user, $plan, false);

        $this->assertNotNull($subscription);
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals($plan->id, $subscription->plan_id);
        $this->assertEquals('active', $subscription->status);
        $this->assertNotNull($subscription->ends_at);
    }

    public function test_create_subscription_creates_trial_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->withTrial(7)->create();

        $subscription = $this->service->createSubscription($user, $plan, true);

        $this->assertEquals('trial', $subscription->status);
        $this->assertNotNull($subscription->trial_ends_at);
        $this->assertNull($subscription->ends_at);
    }

    public function test_create_subscription_updates_existing_subscription()
    {
        $user = User::factory()->create();
        $oldPlan = Plan::factory()->create();
        $newPlan = Plan::factory()->create();

        $oldSubscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
        ]);

        $subscription = $this->service->createSubscription(
            $user,
            $newPlan,
            false,
        );

        $this->assertEquals($oldSubscription->id, $subscription->id);
        $this->assertEquals($newPlan->id, $subscription->plan_id);
    }

    public function test_create_subscription_preserves_ends_at_when_changing_plan()
    {
        $user = User::factory()->create();
        $oldPlan = Plan::factory()->create();
        $newPlan = Plan::factory()->free()->create();

        $futureEndsAt = now()->addMonth();

        $oldSubscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
            'ends_at' => $futureEndsAt,
        ]);

        $subscription = $this->service->createSubscription(
            $user,
            $newPlan,
            false,
        );

        $this->assertEquals(
            $futureEndsAt->format('Y-m-d H:i:s'),
            $subscription->ends_at->format('Y-m-d H:i:s'),
        );
        $this->assertArrayHasKey('previous_plan_id', $subscription->metadata);
        $this->assertEquals(
            $oldPlan->id,
            $subscription->metadata['previous_plan_id'],
        );
    }

    public function test_create_subscription_adds_plan_to_used_trials_when_trial_activated()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->withTrial(7)->create();

        $subscription = $this->service->createSubscription($user, $plan, true);

        $this->assertArrayHasKey('used_trials', $subscription->metadata);
        $this->assertContains(
            $plan->id,
            $subscription->metadata['used_trials'],
        );
    }

    public function test_create_subscription_does_not_create_trial_if_already_used()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->withTrial(7)->create();

        // Первый раз - создаем триал
        $firstSubscription = $this->service->createSubscription(
            $user,
            $plan,
            true,
        );
        $this->assertEquals('trial', $firstSubscription->status);

        // Второй раз - триал уже использован (refresh чтобы relation не был stale)
        $user->refresh();
        $secondSubscription = $this->service->createSubscription(
            $user,
            $plan,
            true,
        );
        $this->assertEquals('active', $secondSubscription->status);
    }

    public function test_has_used_trial_for_plan_returns_false_when_no_subscription()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $result = $this->service->hasUsedTrialForPlan($user, $plan);

        $this->assertFalse($result);
    }

    public function test_has_used_trial_for_plan_returns_true_when_plan_in_used_trials()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()
            ->withUsedTrials([$plan->id])
            ->create([
                'user_id' => $user->id,
            ]);

        $result = $this->service->hasUsedTrialForPlan($user, $plan);

        $this->assertTrue($result);
    }

    public function test_check_limit_returns_false_when_no_subscription()
    {
        $user = User::factory()->create();

        $result = $this->service->checkLimit($user, 'max_locations');

        $this->assertFalse($result);
    }

    public function test_check_limit_returns_false_when_metric_not_in_plan()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);
        // Plan has no PlanFeature for max_locations, so metric is unavailable

        $result = $this->service->checkLimit($user, 'max_locations');

        $this->assertFalse($result);
    }

    public function test_check_limit_returns_true_when_limit_is_unlimited()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        PlanFeature::factory()
            ->unlimited()
            ->forMetricKey('max_locations')
            ->create([
                'plan_id' => $plan->id,
            ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $result = $this->service->checkLimit($user, 'max_locations');

        $this->assertTrue($result);
    }

    public function test_check_limit_returns_true_when_usage_below_limit()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        PlanFeature::factory()
            ->integer(10)
            ->forMetricKey('max_locations')
            ->create([
                'plan_id' => $plan->id,
            ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Создаем usage с текущим использованием 5
        SubscriptionUsage::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'feature_key' => 'max_locations',
            'current_usage' => 5,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $result = $this->service->checkLimit($user, 'max_locations');

        $this->assertTrue($result);
    }

    public function test_check_limit_returns_false_when_usage_exceeds_limit()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // max_locations_per_month — месячная метрика, usage берётся из SubscriptionUsage
        PlanFeature::factory()
            ->integer(10)
            ->forMetricKey('max_locations_per_month')
            ->create([
                'plan_id' => $plan->id,
            ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        SubscriptionUsage::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'feature_key' => 'max_locations_per_month',
            'current_usage' => 15,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth()->addDay(),
        ]);

        $result = $this->service->checkLimit($user, 'max_locations_per_month');

        $this->assertFalse($result);
    }

    public function test_cancel_subscription_sets_cancelled_at()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $result = $this->service->cancelSubscription($user);

        $this->assertTrue($result);
        $subscription = $user->subscription->fresh();
        $this->assertNotNull($subscription->cancelled_at);
    }

    public function test_cancel_subscription_returns_false_for_free_plan()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->free()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $result = $this->service->cancelSubscription($user);

        $this->assertFalse($result);
    }

    public function test_cancel_subscription_returns_false_when_already_cancelled()
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        Subscription::factory()
            ->cancelled()
            ->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

        $result = $this->service->cancelSubscription($user);

        $this->assertFalse($result);
    }

    public function test_cancel_subscription_returns_false_when_no_active_subscription()
    {
        $user = User::factory()->create();

        $result = $this->service->cancelSubscription($user);

        $this->assertFalse($result);
    }

    public function test_reset_monthly_usage_removes_old_periods_and_creates_current_month()
    {
        $metric = SubscriptionMetric::factory()->create([
            'key' => 'max_appointments_per_month',
            'type' => 'integer',
        ]);
        $plan = Plan::factory()->create();
        PlanFeature::create([
            'plan_id' => $plan->id,
            'metric_id' => $metric->id,
            'value' => '100',
        ]);
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $twoMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth();
        SubscriptionUsage::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'feature_key' => 'max_appointments_per_month',
            'period_start' => $lastMonth,
            'period_end' => $lastMonth->copy()->endOfMonth(),
            'current_usage' => 5,
        ]);
        SubscriptionUsage::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'feature_key' => 'max_appointments_per_month',
            'period_start' => $twoMonthsAgo,
            'period_end' => $twoMonthsAgo->copy()->endOfMonth(),
            'current_usage' => 3,
        ]);

        $this->service->resetMonthlyUsage($user);

        $currentPeriodStart = Carbon::now()->startOfMonth();
        $usages = SubscriptionUsage::where('user_id', $user->id)
            ->where('feature_key', 'max_appointments_per_month')
            ->get();
        $this->assertCount(1, $usages);
        $this->assertEquals($currentPeriodStart->format('Y-m-d'), $usages->first()->period_start->format('Y-m-d'));
        $this->assertEquals(0, $usages->first()->current_usage);
    }
}
