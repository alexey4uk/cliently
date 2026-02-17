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
        $response->assertSee('Главная');
    }

    public function test_dashboard_requires_auth()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
