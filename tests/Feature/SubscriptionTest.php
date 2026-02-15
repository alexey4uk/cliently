<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем необходимые права и роли (client.access нужен для OnlyClientAccess middleware)
        Permission::firstOrCreate([
            'name' => 'client.access',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'client.subscription.view',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'client.subscription.manage',
            'guard_name' => 'web',
        ]);

        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->syncPermissions([
            'client.access',
            'client.subscription.view',
            'client.subscription.manage',
        ]);
    }

    /**
     * Создать пользователя с бизнесом и ролью owner
     */
    protected function createUserWithBusiness(): array
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $business = Business::factory()->create(['owner_id' => $user->id]);

        // Создаем роль owner (если еще не создана)
        $ownerRole = \App\Models\BusinessRole::firstOrCreate(
            ['slug' => 'owner'],
            [
                'name' => 'Владелец',
                'is_system' => true,
            ],
        );

        // Права роли owner для подписки (check.business.permission проверяет business_role_permissions)
        $subscriptionPermissions = ['client.subscription.view', 'client.subscription.manage'];
        foreach ($subscriptionPermissions as $perm) {
            \App\Models\BusinessRolePermission::firstOrCreate(
                ['role_id' => $ownerRole->id, 'permission' => $perm],
                ['granted' => true],
            );
        }

        // Привязываем пользователя к бизнесу с ролью owner
        $user->businesses()->attach($business->id, [
            'role_id' => $ownerRole->id,
        ]);

        // Обновляем связи пользователя
        $user->load('businesses');

        // Очищаем все кеши, связанные с пользователем и бизнесом
        $user->clearSubscriptionCache();
        \Illuminate\Support\Facades\Cache::forget(
            "user_businesses_{$user->id}",
        );
        \Illuminate\Support\Facades\Cache::forget(
            "current_business_{$user->id}",
        );
        \Illuminate\Support\Facades\Cache::forget(
            "current_business_role_{$user->id}_{$business->id}",
        );
        \Illuminate\Support\Facades\Cache::forget(
            "business_user_pivot_{$user->id}_{$business->id}",
        );
        \Illuminate\Support\Facades\Cache::forget(
            "business_owner_{$business->id}",
        );
        \Illuminate\Support\Facades\Cache::forget("business_{$business->id}");
        \Illuminate\Support\Facades\Cache::forget(
            "business_role_{$ownerRole->id}",
        );
        \Illuminate\Support\Facades\Cache::forget('business_role_slug_owner');

        return ['user' => $user, 'business' => $business, 'role' => $ownerRole];
    }

    /**
     * Закрыть только «лишние» буферы вывода до уровня $level (устраняет предупреждение PHPUnit).
     */
    protected function closeOutputBuffers(int $level): void
    {
        while (ob_get_level() > $level) {
            ob_end_clean();
        }
    }

    /**
     * Цепочка с сессией текущего бизнеса (нужно для check.business.permission).
     */
    protected function withBusinessSession(array $data): static
    {
        $business = $data['business'] ?? null;
        if (! $business) {
            throw new \InvalidArgumentException(
                'createUserWithBusiness must return business',
            );
        }

        return $this->withSession(['current_business_id' => $business->id]);
    }

    public function test_subscription_index_page_can_be_rendered()
    {
        $obLevel = ob_get_level();

        [
            'user' => $user,
            'business' => $business,
            'role' => $role,
        ] = $this->createUserWithBusiness();

        // Проверяем, что роль owner имеет права
        $service = app(\App\Services\BusinessRolePermissionService::class);
        $this->assertTrue(
            $service->hasPermission($role->id, 'client.subscription.view'),
            'Owner role should have subscription.view permission',
        );

        // Проверяем, что пользователь имеет бизнес
        $this->assertTrue(
            $user->businesses->contains($business->id),
            'User should have business',
        );

        // Проверяем, что pivot данные записаны в БД
        $pivotData = \Illuminate\Support\Facades\DB::table('business_user')
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->first();
        $this->assertNotNull($pivotData, 'Pivot data should exist');
        $this->assertEquals(
            $role->id,
            $pivotData->role_id,
            'Pivot should have correct role_id',
        );

        Plan::factory()->count(3)->create();
        SubscriptionMetric::factory()
            ->count(2)
            ->create(['is_active' => true]);

        $user->load('businesses');

        $response = $this->withBusinessSession(
            compact('user', 'business', 'role'),
        )
            ->actingAs($user)
            ->get('/subscription');

        $response->assertStatus(200);
        $response->assertViewIs('subscription.index');

        $this->closeOutputBuffers($obLevel);
    }

    public function test_subscription_index_shows_current_plan()
    {
        $obLevel = ob_get_level();

        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get('/subscription');

        $response->assertStatus(200);
        $response->assertSee($plan->name);

        $this->closeOutputBuffers($obLevel);
    }

    public function test_subscription_show_page_can_be_rendered()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->create();

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get("/subscription/{$plan->id}");

        $response->assertStatus(200);
        $response->assertViewIs('subscription.show');
    }

    public function test_subscription_show_redirects_when_plan_inactive()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->inactive()->create();

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get("/subscription/{$plan->id}");

        $response->assertRedirect(route('subscription.index'));
        $response->assertSessionHas('error');
    }

    public function test_subscribe_to_free_plan_activates_immediately()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->free()->create();

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post("/subscription/{$plan->id}/subscribe");

        $response->assertRedirect(route('subscription.current'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_subscribe_to_plan_with_trial_activates_trial()
    {
        Notification::fake();

        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->withTrial(7)->create();

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post("/subscription/{$plan->id}/subscribe", [
                'use_trial' => '1',
            ]);

        $response->assertRedirect(route('subscription.current'));
        $response->assertSessionHas('success');

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertEquals('trial', $subscription->status);
        $this->assertNotNull($subscription->trial_ends_at);
    }

    public function test_subscribe_to_plan_with_trial_skips_trial_if_already_used()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->withTrial(7)->create();

        $this->withBusinessSession($data)
            ->actingAs($user)
            ->post("/subscription/{$plan->id}/subscribe", [
                'use_trial' => '1',
            ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post("/subscription/{$plan->id}/subscribe", [
                'use_trial' => '1',
            ]);

        $response->assertRedirect();
    }

    public function test_subscribe_to_free_blocked_when_active_paid_subscription()
    {
        // При активной платной подписке переход на бесплатный тариф блокируется — сначала отмена на странице «Текущая подписка»
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $oldPlan = Plan::factory()->create();
        $newPlan = Plan::factory()->free()->create();

        $futureEndsAt = now()->addMonth();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $oldPlan->id,
            'ends_at' => $futureEndsAt,
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post("/subscription/{$newPlan->id}/subscribe");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertEquals($oldPlan->id, $subscription->plan_id);
        $this->assertEquals(
            $futureEndsAt->format('Y-m-d H:i:s'),
            $subscription->ends_at->format('Y-m-d H:i:s'),
        );
    }

    public function test_current_subscription_page_can_be_rendered()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get('/subscription/current');

        $response->assertStatus(200);
        $response->assertViewIs('subscription.current');
    }

    public function test_current_subscription_page_shows_only_plan_metrics()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;
        $metric = \App\Models\SubscriptionMetric::factory()->create([
            'key' => 'max_locations',
            'type' => 'integer',
            'label' => 'Локации',
        ]);
        $plan = Plan::factory()->create();
        \App\Models\PlanFeature::create([
            'plan_id' => $plan->id,
            'metric_id' => $metric->id,
            'value' => '5',
        ]);
        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get('/subscription/current');

        $response->assertStatus(200);
        $response->assertViewIs('subscription.current');
        $response->assertViewHas('metricsInPlan');
        $response->assertSee('Использование лимитов');
        $metricsInPlan = $response->viewData('metricsInPlan');
        $this->assertCount(1, $metricsInPlan);
        $this->assertEquals('max_locations', $metricsInPlan[0]['metric']->key);
    }

    public function test_current_subscription_redirects_when_no_subscription()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->get('/subscription/current');

        $response->assertRedirect(route('subscription.index'));
        $response->assertSessionHas('info');
    }

    public function test_cancel_subscription_sets_cancelled_at()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post('/subscription/cancel');

        $response->assertRedirect(route('subscription.current'));
        $response->assertSessionHas('success');

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertNotNull($subscription->cancelled_at);
    }

    public function test_cancel_subscription_returns_error_for_free_plan()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->free()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post('/subscription/cancel');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_renew_subscription_creates_invoice()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        \Illuminate\Support\Facades\Config::set('bepaid.enabled', true);
        \Illuminate\Support\Facades\Config::set('bepaid.shop_id', 'test');
        \Illuminate\Support\Facades\Config::set('bepaid.secret_key', 'test');

        $this->withBusinessSession($data)
            ->actingAs($user)
            ->post('/subscription/renew');

        $this->assertDatabaseHas('invoices', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_renew_subscription_returns_error_for_free_plan()
    {
        $data = $this->createUserWithBusiness();
        ['user' => $user] = $data;

        $plan = Plan::factory()->free()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = $this->withBusinessSession($data)
            ->actingAs($user)
            ->post('/subscription/renew');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
