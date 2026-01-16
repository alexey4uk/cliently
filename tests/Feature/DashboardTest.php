<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_rendered()
    {
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
        $settings = json_decode($user->dashboard_settings, true);
        $this->assertTrue($settings['dashboard']['widgets']['stats_header']);
    }

    public function test_dashboard_refresh_clears_cache()
    {
        $user = User::factory()->create();
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
