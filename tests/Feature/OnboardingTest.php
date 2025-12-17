<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Location;
use App\Models\Master;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_onboarding_business_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/onboarding/business');

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_onboarding_when_already_onboarded(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get('/onboarding/business');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_create_business(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/onboarding/business', [
            'name' => 'Test Business',
            'slug' => 'test-business',
            'description' => 'Test description',
            'phone' => '+375291234567',
            'email' => 'business@test.com',
        ]);

        $response->assertRedirect(route('onboarding.location'));
        $this->assertDatabaseHas('businesses', [
            'name' => 'Test Business',
            'slug' => 'test-business',
        ]);
        $this->assertTrue($user->businesses()->where('slug', 'test-business')->exists());
    }

    public function test_business_validation_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/onboarding/business', [
            'slug' => 'test-business',
            'phone' => '+375291234567',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_business_validation_requires_unique_slug(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($user)->post('/onboarding/business', [
            'name' => 'Test Business',
            'slug' => 'existing-slug',
            'phone' => '+375291234567',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_business_validation_requires_valid_slug_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/onboarding/business', [
            'name' => 'Test Business',
            'slug' => 'Invalid Slug!',
            'phone' => '+375291234567',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_user_cannot_access_location_without_business(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/onboarding/location');

        $response->assertRedirect(route('onboarding.business'));
    }

    public function test_user_can_create_location(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->post('/onboarding/location', [
            'name' => 'Test Location',
            'address' => 'Test Address 123',
            'description' => 'Test location description',
            'phone' => '+375291234567',
            'email' => 'location@test.com',
            'working_hours' => [
                'from' => '09:00',
                'to' => '18:00',
            ],
        ]);

        $response->assertRedirect(route('onboarding.service'));
        $this->assertDatabaseHas('locations', [
            'business_id' => $business->id,
            'name' => 'Test Location',
            'address' => 'Test Address 123',
        ]);
    }

    public function test_location_validation_requires_name(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->post('/onboarding/location', [
            'address' => 'Test Address',
            'phone' => '+375291234567',
            'working_hours' => [
                'from' => '09:00',
                'to' => '18:00',
            ],
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_cannot_access_service_without_location(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->get('/onboarding/service');

        $response->assertRedirect(route('onboarding.location'));
    }

    public function test_user_can_create_service(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/onboarding/service', [
            'name' => 'Test Service',
            'description' => 'Test service description',
            'duration' => 60,
            'price' => 1000,
        ]);

        $response->assertRedirect(route('onboarding.master'));
        $this->assertDatabaseHas('services', [
            'business_id' => $business->id,
            'name' => 'Test Service',
            'duration' => 60,
            'price' => 1000,
        ]);
    }

    public function test_service_validation_requires_name(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/onboarding/service', [
            'duration' => 60,
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_service_validation_requires_valid_duration(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/onboarding/service', [
            'name' => 'Test Service',
            'duration' => 10, // меньше минимума 15
            'price' => 1000,
        ]);

        $response->assertSessionHasErrors('duration');
    }

    public function test_user_cannot_access_master_without_service(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get('/onboarding/master');

        $response->assertRedirect(route('onboarding.service'));
    }

    public function test_user_can_create_master(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/onboarding/master', [
            'name' => 'Test Master',
            'specialization' => 'Hair Stylist',
            'description' => 'Test master description',
            'phone' => '+375291234567',
            'email' => 'master@test.com',
        ]);

        $response->assertRedirect(route('onboarding.complete'));
        $this->assertDatabaseHas('masters', [
            'business_id' => $business->id,
            'name' => 'Test Master',
            'specialization' => 'Hair Stylist',
        ]);
        
        $master = Master::where('name', 'Test Master')->first();
        $this->assertTrue($master->locations()->where('location_id', $location->id)->exists());
        $this->assertTrue($master->services()->where('service_id', $service->id)->exists());
    }

    public function test_master_validation_requires_name(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->post('/onboarding/master', [
            'specialization' => 'Hair Stylist',
            'phone' => '+375291234567',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_complete_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);
        $location = Location::factory()->create(['business_id' => $business->id]);
        $service = Service::factory()->create(['business_id' => $business->id]);
        $master = Master::factory()->create(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get('/onboarding/complete');

        $response->assertStatus(200);
    }

    public function test_full_onboarding_flow(): void
    {
        $user = User::factory()->create();

        // Step 1: Create business
        $response = $this->actingAs($user)->post('/onboarding/business', [
            'name' => 'Full Test Business',
            'slug' => 'full-test-business',
            'description' => 'Full test description',
            'phone' => '+375291234567',
            'email' => 'business@test.com',
        ]);
        $response->assertRedirect(route('onboarding.location'));

        // Step 2: Create location
        $response = $this->actingAs($user)->post('/onboarding/location', [
            'name' => 'Full Test Location',
            'address' => 'Full Test Address',
            'description' => 'Full Test Location Description',
            'phone' => '+375291234567',
            'working_hours' => [
                'from' => '09:00',
                'to' => '18:00',
            ],
        ]);
        $response->assertRedirect(route('onboarding.service'));

        // Step 3: Create service
        $response = $this->actingAs($user)->post('/onboarding/service', [
            'name' => 'Full Test Service',
            'duration' => 90,
            'price' => 2000,
        ]);
        $response->assertRedirect(route('onboarding.master'));

        // Step 4: Create master
        $response = $this->actingAs($user)->post('/onboarding/master', [
            'name' => 'Full Test Master',
            'specialization' => 'Full Test Specialization',
            'phone' => '+375291234567',
        ]);
        $response->assertRedirect(route('onboarding.complete'));

        // Verify all data was created
        $business = Business::where('slug', 'full-test-business')->first();
        $this->assertNotNull($business);
        $this->assertTrue($business->locations()->exists());
        $this->assertTrue($business->services()->exists());
        $this->assertTrue($business->masters()->exists());
    }

    public function test_user_redirected_to_next_step_when_step_already_completed(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        // User already has business, should be redirected to location step
        $response = $this->actingAs($user)->get('/onboarding/business');
        $response->assertRedirect(route('onboarding.location'));

        // User already has location, should be redirected to service step
        $location = Location::factory()->create(['business_id' => $business->id]);
        $response = $this->actingAs($user)->get('/onboarding/location');
        $response->assertRedirect(route('onboarding.service'));

        // User already has service, should be redirected to master step
        $service = Service::factory()->create(['business_id' => $business->id]);
        $response = $this->actingAs($user)->get('/onboarding/service');
        $response->assertRedirect(route('onboarding.master'));

        // User already has master, should be redirected to dashboard (middleware redirects completed users)
        $master = Master::factory()->create(['business_id' => $business->id]);
        $response = $this->actingAs($user)->get('/onboarding/master');
        $response->assertRedirect(route('dashboard'));
    }
}

