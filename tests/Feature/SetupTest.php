<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Бесплатный',
                'description' => 'Для начинающих',
                'price' => 0,
                'interval' => 'monthly',
                'trial_days' => 0,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );
    }

    public function test_setup_screen_can_be_rendered_when_no_admin(): void
    {
        $response = $this->get(route('setup'));

        $response->assertStatus(200);
    }

    public function test_setup_creates_admin_and_redirects_to_panel(): void
    {
        $response = $this->post(route('setup'), [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('panel.index'));
        $this->assertAuthenticated();

        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));
    }

    public function test_setup_redirects_to_home_when_admin_exists(): void
    {
        User::factory()->create()->assignRole('admin');

        $this->get(route('setup'))->assertRedirect('/');
        $this->post(route('setup'), [
            'name' => 'Another',
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');
    }
}
