<?php

namespace Tests\Feature\Auth;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // Создаем право и роль для теста
        Permission::firstOrCreate(['name' => 'client.access', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $role->givePermissionTo('client.access');

        // Создаем план по умолчанию для теста
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Бесплатный',
                'description' => 'Для начинающих и малого бизнеса',
                'price' => null,
                'interval' => 'monthly',
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice', absolute: false));
    }
}
