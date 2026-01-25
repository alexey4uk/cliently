<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\BusinessUserInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');
    }

    public function test_invitation_accept_page_is_accessible_without_authentication()
    {
        // Создаем тестовые данные
        $business = Business::factory()->create();
        $user = User::factory()->create();
        
        $role = BusinessRole::create([
            'slug' => 'master',
            'name' => 'Мастер',
            'is_system' => true,
        ]);

        // Создаем приглашение
        $invitation = BusinessUserInvitation::create([
            'business_id' => $business->id,
            'email' => 'test@example.com',
            'role' => 'master',
            'role_id' => $role->id,
            'token' => BusinessUserInvitation::generateToken(),
            'created_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);

        // Пытаемся получить доступ к странице приглашения без аутентификации
        $response = $this->get('/invite/' . $invitation->token);

        // Должно вернуть успешный ответ, а не редирект на логин
        $response->assertStatus(200);
        $response->assertViewIs('auth.accept-invitation');
        $response->assertViewHas('invitation', function ($viewInvitation) use ($invitation) {
            return $viewInvitation->id === $invitation->id;
        });
    }

    public function test_invitation_activate_works_for_new_users()
    {
        // Создаем тестовые данные
        $business = Business::factory()->create();
        $user = User::factory()->create();
        
        $role = BusinessRole::create([
            'slug' => 'master',
            'name' => 'Мастер',
            'is_system' => true,
        ]);

        // Создаем приглашение
        $invitation = BusinessUserInvitation::create([
            'business_id' => $business->id,
            'email' => 'newuser@example.com',
            'role' => 'master',
            'role_id' => $role->id,
            'token' => BusinessUserInvitation::generateToken(),
            'created_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);

        // Пытаемся активировать приглашение без аутентификации
        $response = $this->post('/invite/' . $invitation->token . '/activate', [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Должно перенаправить на дашборд после успешной активации
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        $invitation->refresh();
        $this->assertNotNull($invitation->accepted_at);
    }
}