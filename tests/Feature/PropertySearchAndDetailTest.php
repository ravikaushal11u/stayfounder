<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyRoom;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySearchAndDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertySeeder::class);
    }

    public function test_homepage_renders_featured_properties(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('Sunrise Student Living');
        $response->assertSeeText('Verified Student Listings');
    }

    public function test_stays_search_page_renders_with_filters(): void
    {
        $response = $this->get('/stays');
        $response->assertStatus(200);
        $response->assertSeeText('All Student Accommodations');
        $response->assertSeeText('Filters');
        $response->assertSeeText('Accommodation Type');
        $response->assertSeeText('Gender Preference');
        $response->assertSeeText('Max Rent');
    }

    public function test_can_filter_properties_by_city(): void
    {
        $response = $this->get('/stays?city=Pune');
        $response->assertStatus(200);
        $response->assertSeeText('Sunrise Student Living');
        $response->assertDontSeeText('DU North Campus');
    }

    public function test_can_filter_properties_by_type(): void
    {
        $response = $this->get('/stays?type=hostel');
        $response->assertStatus(200);
        $response->assertSeeText('Scholar Girls Hostel');
        $response->assertDontSeeText('DU North Campus 2BHK');
    }

    public function test_can_filter_properties_by_budget(): void
    {
        $response = $this->get('/stays?budget=7000');
        $response->assertStatus(200);
        // Properties with min rent <= 7000 should appear
        $response->assertSeeText('DU North Campus 2BHK');
    }

    public function test_can_filter_properties_by_gender(): void
    {
        $response = $this->get('/stays?gender=female');
        $response->assertStatus(200);
        $response->assertSeeText('Scholar Girls Hostel');
        $response->assertDontSeeText('Aspirants Coaching Residency & PG'); // male only
    }

    public function test_can_filter_properties_by_food_inclusion(): void
    {
        $response = $this->get('/stays?food=1');
        $response->assertStatus(200);
        $response->assertSeeText('Sunrise Student Living');
        $response->assertDontSeeText('DU North Campus 2BHK'); // food not included
    }

    public function test_can_filter_properties_by_amenities(): void
    {
        $response = $this->get('/stays?amenities[]=biometric-warden');
        $response->assertStatus(200);
        $response->assertSeeText('Scholar Girls Hostel');
    }

    public function test_can_filter_properties_by_distance_radius(): void
    {
        // Pune coordinates near Viman Nagar: lat 18.5679, lng 73.9143
        $response = $this->get('/stays?lat=18.5679&lng=73.9143&distance=3');
        $response->assertStatus(200);
        $response->assertSeeText('Sunrise Student Living');
        // Delhi property is thousands of kms away, should not appear
        $response->assertDontSeeText('DU North Campus');
    }

    public function test_property_detail_page_loads_with_rooms_and_amenities(): void
    {
        $property = Property::where('slug', 'sunrise-student-living-pg-pune')->first();
        $this->assertNotNull($property);

        $response = $this->get('/stay/' . $property->slug);
        $response->assertStatus(200);
        $response->assertSeeText($property->title);
        $response->assertSeeText('Room Sharing Options');
        $response->assertSeeText('Amenities & Facilities Included', false);
        $response->assertSeeText('Distance From Nearby Colleges');
        $response->assertSeeText('Chat with Provider');
        $response->assertSeeText('Call Owner');
        $response->assertSeeText('Never transfer advance money or gate deposit');
    }

    public function test_property_view_count_increments_on_visit(): void
    {
        $property = Property::where('slug', 'sunrise-student-living-pg-pune')->first();
        $initialViews = $property->views_count;

        $this->get('/stay/' . $property->slug);

        $this->assertEquals($initialViews + 1, $property->fresh()->views_count);
    }

    public function test_inactive_property_returns_404(): void
    {
        $property = Property::first();
        $property->update(['status' => 'draft']);

        $response = $this->get('/stay/' . $property->slug);
        $response->assertStatus(404);
    }

    public function test_can_filter_properties_by_pincode(): void
    {
        $property = Property::first();
        $property->update(['pincode' => '825301']);

        $response = $this->get('/stays?query=825301');
        $response->assertStatus(200);
        $response->assertSeeText($property->title);
    }

    public function test_property_detail_renders_360_virtual_tour_when_present(): void
    {
        $property = Property::first();
        $property->update(['image_360' => 'properties/sample_360.jpg']);

        $response = $this->get('/stay/' . $property->slug);
        $response->assertStatus(200);
        $response->assertSeeText('Interactive 360° Virtual Room Tour');
        $response->assertSee('panorama-360-viewer');
    }

    public function test_nearby_locality_search_falls_back_to_proximity_results(): void
    {
        // Seed property in Hazaribagh
        $property = Property::first();
        $property->update([
            'city' => 'Hazaribagh',
            'locality' => 'Sindur',
            'latitude' => 23.9925,
            'longitude' => 85.3637,
        ]);

        // Search for 'Kolghati' which is ~2.5km from Sindur in Hazaribagh
        $response = $this->get('/stays?query=Kolghati');
        $response->assertStatus(200);
        $response->assertSeeText($property->title);
    }
}
