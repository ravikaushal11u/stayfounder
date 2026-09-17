<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyRoom;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProviderPropertyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $provider;
    protected User $otherProvider;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertySeeder::class);

        $this->provider = User::where('role', UserRole::PROVIDER)->first();

        $this->otherProvider = User::create([
            'name' => 'Vikram Patel',
            'email' => 'vikram@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::PROVIDER,
            'status' => UserStatus::ACTIVE,
            'phone' => '+91 99999 11111',
        ]);

        $this->student = User::create([
            'name' => 'Aarav Student',
            'email' => 'aarav@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_provider_properties(): void
    {
        $response = $this->get(route('provider.properties.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_provider_properties(): void
    {
        $response = $this->actingAs($this->student)->get(route('provider.properties.index'));
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_provider_can_view_their_properties_index(): void
    {
        $response = $this->actingAs($this->provider)->get(route('provider.properties.index'));
        $response->assertStatus(200);
        $response->assertSeeText('My Properties');
        $response->assertSeeText('List New Property');
    }

    public function test_provider_can_view_property_creation_page(): void
    {
        $response = $this->actingAs($this->provider)->get(route('provider.properties.create'));
        $response->assertStatus(200);
        $response->assertSeeText('List a Student Accommodation');
        $response->assertSeeText('Basic Property Information');
        $response->assertSeeText('Room Configurations');
        $response->assertSeeText('Upload Property Photos');
    }

    public function test_provider_can_create_property_with_rooms_and_amenities(): void
    {
        Storage::fake('public');

        $amenity = Amenity::first();
        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        $postData = [
            'title' => 'Green Valley Luxury Girls PG',
            'property_type' => 'pg',
            'gender_preference' => 'female',
            'description' => 'Safe, peaceful stay for college girls.',
            'address' => 'Plot 99, FC Road',
            'locality' => 'Shivajinagar',
            'city' => 'Pune',
            'pincode' => '411005',
            'latitude' => 18.5204,
            'longitude' => 73.8567,
            'monthly_rent_min' => 8500,
            'monthly_rent_max' => 12000,
            'security_deposit' => 5000,
            'notice_period_days' => 30,
            'total_beds' => 10,
            'available_beds' => 3,
            'food_included' => 1,
            'food_details' => 'Breakfast and dinner included.',
            'gate_closing_time' => '10:00 PM',
            'rules' => ['No smoking', 'Gate closes at 10 PM'],
            'nearby_colleges' => [
                ['name' => 'Fergusson College', 'distance_km' => 0.4],
                ['name' => 'COEP Tech Univ', 'distance_km' => 1.8],
            ],
            'amenities' => [$amenity->id],
            'rooms' => [
                [
                    'room_type' => 'single',
                    'title' => 'Private AC Room',
                    'monthly_rent' => 12000,
                    'security_deposit' => 6000,
                    'has_attached_bathroom' => 1,
                    'has_ac' => 1,
                    'has_balcony' => 1,
                ],
                [
                    'room_type' => 'double',
                    'title' => 'Double Sharing Non-AC',
                    'monthly_rent' => 8500,
                    'security_deposit' => 4500,
                    'has_attached_bathroom' => 1,
                    'has_ac' => 0,
                    'has_balcony' => 0,
                ],
            ],
            'images' => [
                UploadedFile::fake()->createWithContent('room1.png', $png1x1),
                UploadedFile::fake()->createWithContent('room2.png', $png1x1),
            ],
            'image_360' => UploadedFile::fake()->createWithContent('panorama_360.png', $png1x1),
        ];

        $response = $this->actingAs($this->provider)->post(route('provider.properties.store'), $postData);

        $response->assertRedirect(route('provider.properties.index'));
        $response->assertSessionHas('success');

        $property = Property::where('title', 'Green Valley Luxury Girls PG')->first();
        $this->assertNotNull($property);
        $this->assertEquals($this->provider->id, $property->user_id);
        $this->assertEquals(2, $property->rooms()->count());
        $this->assertEquals(2, $property->images()->count());
        $this->assertNotNull($property->image_360);
        $this->assertNotNull($property->image_360_url);
        $this->assertTrue($property->images()->where('is_primary', true)->exists());
        $this->assertTrue($property->amenities()->where('amenities.id', $amenity->id)->exists());
    }

    public function test_provider_can_edit_their_own_property(): void
    {
        $property = Property::where('user_id', $this->provider->id)->first();
        $this->assertNotNull($property);

        $response = $this->actingAs($this->provider)->get(route('provider.properties.edit', $property->id));
        $response->assertStatus(200);
        $response->assertSeeText('Edit Accommodation');
        $response->assertSeeText($property->title);
    }

    public function test_provider_cannot_edit_another_providers_property(): void
    {
        $property = Property::where('user_id', $this->provider->id)->first();

        // Acting as another provider should be denied (403)
        $response = $this->actingAs($this->otherProvider)->get(route('provider.properties.edit', $property->id));
        $response->assertStatus(403);
    }

    public function test_provider_can_toggle_property_status(): void
    {
        $property = Property::where('user_id', $this->provider->id)->first();
        $initialStatus = $property->status;

        $response = $this->actingAs($this->provider)->post(route('provider.properties.toggle-status', $property->id));
        $response->assertRedirect();

        $newStatus = $property->fresh()->status;
        $this->assertNotEquals($initialStatus, $newStatus);
    }

    public function test_provider_can_delete_their_property(): void
    {
        $property = Property::where('user_id', $this->provider->id)->first();

        $response = $this->actingAs($this->provider)->delete(route('provider.properties.destroy', $property->id));
        $response->assertRedirect(route('provider.properties.index'));

        $this->assertSoftDeleted('properties', ['id' => $property->id]);
    }

    public function test_provider_can_set_primary_image_and_delete_image(): void
    {
        Storage::fake('public');

        $property = Property::where('user_id', $this->provider->id)->first();

        $img1 = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/test1.jpg',
            'is_primary' => true,
        ]);

        $img2 = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/test2.jpg',
            'is_primary' => false,
        ]);

        // Set img2 as primary
        $response = $this->actingAs($this->provider)->post(route('provider.properties.images.primary', [$property->id, $img2->id]));
        $response->assertRedirect();

        $this->assertFalse((bool) $img1->fresh()->is_primary);
        $this->assertTrue((bool) $img2->fresh()->is_primary);

        // Delete img1
        $delResponse = $this->actingAs($this->provider)->delete(route('provider.properties.images.delete', [$property->id, $img1->id]));
        $delResponse->assertRedirect();

        $this->assertDatabaseMissing('property_images', ['id' => $img1->id]);
    }
}
