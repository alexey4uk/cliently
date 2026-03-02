<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('payments.gateways.bepaid.available', true);
        Config::set('payments.gateways.bepaid.shop_id', 'test_shop');
        Config::set('payments.gateways.bepaid.secret_key', 'test_secret');
    }

    protected function getBasicAuthHeader(): string
    {
        return 'Basic '.base64_encode(config('payments.gateways.bepaid.shop_id').':'.config('payments.gateways.bepaid.secret_key'));
    }

    public function test_webhook_activates_subscription_after_payment()
    {
        Notification::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        // Инвойс в pending, чтобы webhook обработался и вызвал handler (создание подписки)
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_type' => 'subscription',
        ]);

        $payload = [
            'transaction' => [
                'uid' => 'test_transaction_123',
                'tracking_id' => 'invoice_'.$invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        $response = $this->postJson('/webhooks/bepaid', $payload, [
            'Authorization' => $this->getBasicAuthHeader(),
        ]);

        $response->assertStatus(200);

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals($plan->id, $subscription->plan_id);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals('paid', $subscription->payment_status);
    }

    public function test_webhook_renews_subscription_from_current_ends_at()
    {
        Notification::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->monthly()->create();

        $futureEndsAt = now()->addMonth();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => $futureEndsAt,
        ]);

        // Инвойс в pending (продление), чтобы webhook вызвал handler и продлил подписку
        $invoice = Invoice::factory()->renewal()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
            'payment_type' => 'subscription',
        ]);

        $payload = [
            'transaction' => [
                'uid' => 'test_transaction_renewal',
                'tracking_id' => 'invoice_'.$invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        $response = $this->postJson('/webhooks/bepaid', $payload, [
            'Authorization' => $this->getBasicAuthHeader(),
        ]);

        $response->assertStatus(200);

        $subscription = Subscription::where('user_id', $user->id)->first();
        // Новый ends_at должен быть на месяц позже от старого ends_at
        $expectedEndsAt = $futureEndsAt->copy()->addMonth();
        $this->assertEquals($expectedEndsAt->format('Y-m-d'), $subscription->ends_at->format('Y-m-d'));
    }

    public function test_webhook_preserves_ends_at_when_changing_plan()
    {
        Notification::fake();

        $user = User::factory()->create();
        $oldPlan = Plan::factory()->monthly()->create();
        $newPlan = Plan::factory()->monthly()->create();

        $futureEndsAt = now()->addMonth();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
            'status' => 'active',
            'ends_at' => $futureEndsAt,
        ]);

        // Инвойс в pending (смена плана), чтобы webhook вызвал handler и обновил план с сохранением ends_at
        $invoice = Invoice::factory()->planChange($oldPlan->id, $oldPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $newPlan->id,
            'subscription_id' => $subscription->id,
            'payment_type' => 'subscription',
        ]);

        $payload = [
            'transaction' => [
                'uid' => 'test_transaction_plan_change',
                'tracking_id' => 'invoice_'.$invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        $response = $this->postJson('/webhooks/bepaid', $payload, [
            'Authorization' => $this->getBasicAuthHeader(),
        ]);

        $response->assertStatus(200);

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertEquals($newPlan->id, $subscription->plan_id);
        $this->assertEquals($futureEndsAt->format('Y-m-d H:i:s'), $subscription->ends_at->format('Y-m-d H:i:s'));
        $this->assertArrayHasKey('previous_plan_id', $subscription->metadata);
    }

    public function test_webhook_returns_401_without_authentication()
    {
        $payload = [
            'transaction' => [
                'uid' => 'test_transaction_123',
                'status' => 'successful',
            ],
            'order' => [
                'id' => '1',
            ],
        ];

        $response = $this->postJson('/webhooks/bepaid', $payload);

        $response->assertStatus(401);
    }

    public function test_webhook_returns_401_with_invalid_credentials()
    {
        $payload = [
            'transaction' => [
                'uid' => 'test_transaction_123',
                'status' => 'successful',
            ],
            'order' => [
                'id' => '1',
            ],
        ];

        $response = $this->postJson('/webhooks/bepaid', $payload, [
            'Authorization' => 'Basic '.base64_encode('wrong:credentials'),
        ]);

        $response->assertStatus(401);
    }
}
