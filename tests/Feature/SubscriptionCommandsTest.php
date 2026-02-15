<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_expired_trials_command()
    {
        Notification::fake();

        $user = User::factory()->create();
        $trialPlan = Plan::factory()->withTrial(7)->create();
        $freePlan = Plan::factory()->free()->create();

        // Создаем истекший триал
        Subscription::factory()->expiredTrial()->create([
            'user_id' => $user->id,
            'plan_id' => $trialPlan->id,
        ]);

        Artisan::call('subscription:process-expired-trials');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
        ]);
    }

    public function test_process_expired_trials_preserves_used_trials_metadata()
    {
        Notification::fake();

        $user = User::factory()->create();
        $trialPlan = Plan::factory()->withTrial(7)->create();
        $freePlan = Plan::factory()->free()->create();

        // Создаем истекший триал с used_trials в metadata
        Subscription::factory()->withUsedTrials([$trialPlan->id])->expiredTrial()->create([
            'user_id' => $user->id,
            'plan_id' => $trialPlan->id,
        ]);

        Artisan::call('subscription:process-expired-trials');

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertArrayHasKey('used_trials', $subscription->metadata);
        $this->assertContains($trialPlan->id, $subscription->metadata['used_trials']);
    }

    public function test_process_expired_subscriptions_command()
    {
        Notification::fake();

        $user = User::factory()->create();
        $paidPlan = Plan::factory()->create(['price' => 50]);
        $freePlan = Plan::factory()->free()->create();

        // Создаем истекшую платную подписку
        Subscription::factory()->expired()->create([
            'user_id' => $user->id,
            'plan_id' => $paidPlan->id,
        ]);

        Artisan::call('subscription:process-expired');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
        ]);
    }

    public function test_process_expired_subscriptions_does_not_process_free_plans()
    {
        Notification::fake();

        $user = User::factory()->create();
        $freePlan = Plan::factory()->free()->create();

        // Создаем истекшую бесплатную подписку
        Subscription::factory()->expired()->create([
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
        ]);

        Artisan::call('subscription:process-expired');

        // Подписка должна остаться на бесплатном тарифе
        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertEquals($freePlan->id, $subscription->plan_id);
    }

    public function test_process_expired_subscriptions_saves_expired_metadata()
    {
        Notification::fake();

        $user = User::factory()->create();
        $paidPlan = Plan::factory()->create(['price' => 50]);
        $freePlan = Plan::factory()->free()->create();

        $expiredEndsAt = now()->subDay();

        Subscription::factory()->expired()->create([
            'user_id' => $user->id,
            'plan_id' => $paidPlan->id,
            'ends_at' => $expiredEndsAt,
        ]);

        Artisan::call('subscription:process-expired');

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertArrayHasKey('previous_status', $subscription->metadata);
        $this->assertEquals('expired', $subscription->metadata['previous_status']);
        $this->assertArrayHasKey('expired_at', $subscription->metadata);
    }
}
