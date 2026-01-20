<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_rendered()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);
        
        // Создаём полный onboarding
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Добро пожаловать');
        $response->assertSee($business->name);
    }

    public function test_dashboard_settings_can_be_rendered()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);

        // Создаём полный onboarding
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get('/settings/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Настройки Dashboard');
        $response->assertSee('Видимость виджетов');
        $response->assertSee('Порядок виджетов');
    }

    public function test_dashboard_settings_can_be_updated()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);

        // Создаём полный onboarding
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/settings/dashboard', [
            'widgets' => [
                'stats_header' => true,
                'quick_actions' => true,
                'next_appointment' => true,
                'today_appointments' => true,
                'pending_appointments' => true,
                'recent_clients' => true,
                'weekly_chart' => false,
            ],
            'widget_order' => [
                'next_appointment',
                'today_appointments',
                'pending_appointments',
                'recent_clients',
                'weekly_chart',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Проверяем, что настройки сохранены
        $user->refresh();
        $this->assertNotNull($user->dashboard_settings);
        $this->assertTrue($user->dashboard_settings['dashboard']['widgets']['stats_header']);
    }

    public function test_dashboard_refresh_clears_cache()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);

        // Создаём полный onboarding
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        // Сначала загружаем dashboard (кэшируем данные)
        $this->actingAs($user)->get('/dashboard');

        // Затем обновляем (очищаем кэш)
        $response = $this->actingAs($user)->post('/dashboard/refresh');

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Данные обновлены');
    }

    public function test_dashboard_redirects_to_business_creation_when_no_business()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');

        // Пользователь без бизнеса
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('settings.business.create'));
        $response->assertSessionHas('info', 'Добро пожаловать! Сначала создайте свой бизнес.');
    }

    public function test_dashboard_redirects_to_location_creation_when_no_locations()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);

        // Бизнес есть, но нет локаций
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('settings.locations.create'));
        $response->assertSessionHas('info', 'Добавьте локацию для записи клиентов.');
    }

    public function test_dashboard_redirects_to_service_creation_when_no_services()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);
        Location::factory()->create(['business_id' => $business->id]);

        // Бизнес и локация есть, но нет услуг
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('services.create'));
        $response->assertSessionHas('info', 'Добавьте услуги, которые вы предлагаете.');
    }

    public function test_dashboard_redirects_to_master_creation_when_no_masters()
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        $user = User::factory()->create();
        $user->assignRole('user');
        $business = Business::factory()->create();
        $user->businesses()->attach($business->id);
        Location::factory()->create(['business_id' => $business->id]);
        Service::factory()->create(['business_id' => $business->id]);

        // Бизнес, локация и услуга есть, но нет мастеров
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('settings.masters.create'));
        $response->assertSessionHas('info', 'Добавьте мастеров для предоставления услуг.');
    }

    public function test_dashboard_requires_auth()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_settings_requires_auth()
    {
        $response = $this->get('/settings/dashboard');
        $response->assertRedirect('/login');
    }
}
