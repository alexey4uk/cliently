<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CountrySeeder::class);
    }

    protected function userWithPhone(array $overrides = []): User
    {
        $user = User::factory()->create($overrides);
        $country = Country::where('code', 'BY')->first();
        if ($country) {
            $user->phones()->create([
                'country_id' => $country->id,
                'phone' => '+375291234567',
                'type' => 'primary',
            ]);
        }

        return $user->refresh();
    }

    public function test_profile_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    public function test_profile_requires_authentication(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_user_can_update_profile_name(): void
    {
        $user = $this->userWithPhone([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $countryId = Country::where('code', 'BY')->value('id');

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'New Name',
                'email' => 'old@example.com',
                'phone_country_id' => $countryId,
                'phone' => '+375291234567',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertEquals('New Name', $user->refresh()->name);
        $this->assertEquals('old@example.com', $user->refresh()->email);
    }

    public function test_user_can_update_profile_email(): void
    {
        $user = $this->userWithPhone([
            'name' => 'Test User',
            'email' => 'old@example.com',
        ]);
        $countryId = Country::where('code', 'BY')->value('id');

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'new@example.com',
                'phone_country_id' => $countryId,
                'phone' => '+375291234567',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertEquals('new@example.com', $user->refresh()->email);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }
}
