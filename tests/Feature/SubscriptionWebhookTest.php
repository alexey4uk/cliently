<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Удаляем существующие настройки и создаем заново для чистоты тестов
        \App\Models\BepaidSettings::where('id', 1)->delete();
        
        \App\Models\BepaidSettings::create([
            'id' => 1,
            'test_mode' => true,
            'enabled' => true,
            'test_shop_id' => 'test_shop',
            'test_secret_key' => 'test_secret',
        ]);
    }

    protected function getBasicAuthHeader(): string
    {
        $settings = \App\Models\BepaidSettings::updateOrCreate(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
            ]
        );
        
        // Убеждаемся, что модель обновлена из базы данных
        $settings->refresh();
        
        $current = $settings->getCurrentSettings();

        return 'Basic ' . base64_encode($current['shop_id'] . ':' . $current['secret_key']);
    }

    public function test_webhook_activates_subscription_after_payment()
    {
        Notification::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $invoice = Invoice::factory()->paid()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $payload = [
            'transaction' => [
                'uid' => $invoice->bepaid_transaction_id ?? 'test_transaction_123',
                'tracking_id' => 'invoice_' . $invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        // Убеждаемся, что настройки актуальны перед запросом
        // Используем DB::table для прямого обновления, чтобы избежать кеша моделей
        DB::table('bepaid_settings')->updateOrInsert(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

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

        // Убеждаемся, что настройки актуальны
        \App\Models\BepaidSettings::updateOrCreate(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
            ]
        );

        $user = User::factory()->create();
        $plan = Plan::factory()->monthly()->create();

        $futureEndsAt = now()->addMonth();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => $futureEndsAt,
        ]);

        $invoice = Invoice::factory()->paid()->renewal()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
        ]);

        $payload = [
            'transaction' => [
                'uid' => $invoice->bepaid_transaction_id ?? 'test_transaction_123',
                'tracking_id' => 'invoice_' . $invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        // Убеждаемся, что настройки актуальны перед запросом
        // Используем DB::table для прямого обновления, чтобы избежать кеша моделей
        DB::table('bepaid_settings')->updateOrInsert(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

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

        // Убеждаемся, что настройки актуальны
        \App\Models\BepaidSettings::updateOrCreate(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
            ]
        );

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

        $invoice = Invoice::factory()->paid()->planChange($oldPlan->id, $oldPlan->name)->create([
            'user_id' => $user->id,
            'plan_id' => $newPlan->id,
            'subscription_id' => $subscription->id,
        ]);

        $payload = [
            'transaction' => [
                'uid' => $invoice->bepaid_transaction_id ?? 'test_transaction_123',
                'tracking_id' => 'invoice_' . $invoice->id,
                'status' => 'successful',
                'amount' => (int) round($invoice->amount * 100),
                'currency' => 'BYN',
            ],
            'order' => [
                'id' => (string) $invoice->id,
            ],
        ];

        // Убеждаемся, что настройки актуальны перед запросом
        // Используем DB::table для прямого обновления, чтобы избежать кеша моделей
        DB::table('bepaid_settings')->updateOrInsert(
            ['id' => 1],
            [
                'test_mode' => true,
                'enabled' => true,
                'test_shop_id' => 'test_shop',
                'test_secret_key' => 'test_secret',
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

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
            'Authorization' => 'Basic ' . base64_encode('wrong:credentials'),
        ]);

        $response->assertStatus(401);
    }
}
