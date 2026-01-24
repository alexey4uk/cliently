<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_user_can_update_profile_phone(): void
    {
        $user = $this->userWithPhone([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $countryId = Country::where('code', 'BY')->value('id');

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone_country_id' => $countryId,
                'phone' => '+375299876543',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertEquals('+375299876543', $user->refresh()->phone);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $payload = ['name' => $user->name, 'email' => $user->email];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', $payload + ['avatar' => $file]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        $payload = ['name' => $user->name, 'email' => $user->email, 'remove_avatar' => '1'];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', $payload);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertNull($user->refresh()->avatar);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_avatar_validation_rejects_non_image_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = ['name' => $user->name, 'email' => $user->email, 'avatar' => $file];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)->patch('/profile', $payload);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_avatar_validation_rejects_large_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg')->size(6000); // 6MB
        $payload = ['name' => $user->name, 'email' => $user->email, 'avatar' => $file];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)->patch('/profile', $payload);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_profile_update_requires_name(): void
    {
        $user = User::factory()->create();
        $payload = ['email' => $user->email];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)->patch('/profile', $payload);

        $response->assertSessionHasErrors('name');
    }

    public function test_profile_update_requires_email(): void
    {
        $user = User::factory()->create();
        $payload = ['name' => $user->name];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)->patch('/profile', $payload);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_requires_phone(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        // Phone is nullable, so no error should be present
        $response->assertSessionHasNoErrors();
    }

    public function test_profile_update_validates_email_format(): void
    {
        $user = User::factory()->create();
        $payload = ['name' => $user->name, 'email' => 'invalid-email'];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)->patch('/profile', $payload);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_validates_unique_email(): void
    {
        $user1 = $this->userWithPhone(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $countryId = Country::where('code', 'BY')->value('id');

        $response = $this->actingAs($user1)->patch('/profile', [
            'name' => $user1->name,
            'email' => 'user2@example.com',
            'phone_country_id' => $countryId,
            'phone' => $user1->phone,
        ]);

        $response->assertSessionHasErrors('email');
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

    public function test_password_update_requires_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->patch('/profile/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_password_update_validates_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->patch('/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_password_update_requires_password_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->patch('/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_delete_avatar_via_api(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $path = $file->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        $response = $this->actingAs($user)->delete('/profile/avatar');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNull($user->refresh()->avatar);
    }

    public function test_old_avatar_is_deleted_when_uploading_new_one(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $oldFile = UploadedFile::fake()->image('old-avatar.jpg');
        $oldPath = $oldFile->store('avatars', 'public');
        $user->avatar = $oldPath;
        $user->save();
        $payload = ['name' => $user->name, 'email' => $user->email, 'avatar' => UploadedFile::fake()->image('new-avatar.jpg')];
        if ($user->phone) {
            $payload['phone_country_id'] = $user->primaryPhone->country_id;
            $payload['phone'] = $user->phone;
        }

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', $payload);

        $response->assertRedirect(route('profile.edit'));

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->refresh()->avatar);
    }
}
