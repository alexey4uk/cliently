<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithPanelAccess(?array $extraPermissions = []): User
    {
        Permission::firstOrCreate(['name' => 'panel.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'panel_admin', 'guard_name' => 'web']);
        $role->givePermissionTo('panel.access');
        foreach ($extraPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $role->givePermissionTo($perm);
        }
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_panel_requires_auth(): void
    {
        $response = $this->get('/panel');
        $response->assertRedirect('/login');
    }

    public function test_panel_index_returns_403_without_permission(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/panel');
        $response->assertStatus(403);
    }

    public function test_panel_index_returns_200_with_permission(): void
    {
        $user = $this->createUserWithPanelAccess();
        $response = $this->actingAs($user)->get('/panel');
        $response->assertStatus(200);
        $response->assertViewIs('panel.dashboard');
        $response->assertSee('Панель управления');
    }

    public function test_panel_users_list_returns_200_with_permission(): void
    {
        $user = $this->createUserWithPanelAccess(['panel.users.view']);
        $response = $this->actingAs($user)->get('/panel/users');
        $response->assertStatus(200);
        $response->assertViewIs('panel.users.index');
    }

    public function test_panel_users_list_returns_403_without_permission(): void
    {
        $user = $this->createUserWithPanelAccess();
        $response = $this->actingAs($user)->get('/panel/users');
        $response->assertStatus(403);
    }

    public function test_panel_businesses_list_returns_200_with_permission(): void
    {
        $user = $this->createUserWithPanelAccess(['panel.businesses.view']);
        $response = $this->actingAs($user)->get('/panel/businesses');
        $response->assertStatus(200);
        $response->assertViewIs('panel.businesses.index');
    }
}
