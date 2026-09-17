<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\SavedProperty;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFavoritesAndInquiriesTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $provider;
    protected User $otherProvider;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertySeeder::class);

        $this->provider = User::where('role', UserRole::PROVIDER)->first();
        $this->property = Property::where('user_id', $this->provider->id)->first();

        $this->student = User::create([
            'name' => 'Aarav Student',
            'email' => 'aarav.student@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
            'phone' => '+91 91234 56789',
        ]);

        $this->otherProvider = User::create([
            'name' => 'Other Provider',
            'email' => 'other.provider@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::PROVIDER,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    public function test_guest_cannot_access_saved_stays(): void
    {
        $response = $this->get(route('student.saved-stays'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_toggling_favorite_redirects_or_returns_401(): void
    {
        // Standard request
        $response = $this->post(route('properties.favorite', $this->property->id));
        $response->assertRedirect(route('login'));

        // Ajax request
        $ajaxResponse = $this->postJson(route('properties.favorite', $this->property->id));
        $ajaxResponse->assertStatus(401);
    }

    public function test_student_can_save_and_unsave_property(): void
    {
        // 1. Save property (POST json)
        $response = $this->actingAs($this->student)->postJson(route('properties.favorite', $this->property->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_saved' => true,
            'total_saved' => 1,
        ]);

        $this->assertTrue($this->student->hasSaved($this->property));
        $this->assertDatabaseHas('saved_properties', [
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
        ]);

        // 2. Unsave property (toggle again)
        $unsaveResponse = $this->actingAs($this->student)->postJson(route('properties.favorite', $this->property->id));
        $unsaveResponse->assertStatus(200);
        $unsaveResponse->assertJson([
            'success' => true,
            'is_saved' => false,
            'total_saved' => 0,
        ]);

        $this->assertFalse($this->student->hasSaved($this->property));
        $this->assertDatabaseMissing('saved_properties', [
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
        ]);
    }

    public function test_student_can_view_saved_stays_page_with_comparison(): void
    {
        SavedProperty::create([
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.saved-stays'));
        $response->assertStatus(200);
        $response->assertSeeText('Saved Accommodations');
        $response->assertSeeText($this->property->title);
        $response->assertSeeText('Accommodation Feature Comparison');
    }

    public function test_student_or_guest_can_submit_inquiry_on_property(): void
    {
        $inquiryData = [
            'student_name' => 'Aarav Student',
            'student_phone' => '+91 91234 56789',
            'student_email' => 'aarav.student@stayfinder.com',
            'target_move_in_date' => now()->addDays(5)->format('Y-m-d'),
            'preferred_room_type' => 'double',
            'message' => 'Hello, is double sharing AC room vacant starting this week?',
        ];

        $response = $this->actingAs($this->student)->postJson(route('properties.inquire', $this->property->id), $inquiryData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('property_inquiries', [
            'property_id' => $this->property->id,
            'provider_id' => $this->provider->id,
            'user_id' => $this->student->id,
            'student_name' => 'Aarav Student',
            'status' => 'new',
        ]);
    }

    public function test_property_inquiries_counter_increments_on_new_inquiry(): void
    {
        $initialCount = $this->property->inquiries_count;

        $inquiryData = [
            'student_name' => 'Test Student',
            'student_phone' => '+91 98888 77777',
            'target_move_in_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $this->post(route('properties.inquire', $this->property->id), $inquiryData);

        $this->assertEquals($initialCount + 1, $this->property->fresh()->inquiries_count);
    }

    public function test_student_can_view_their_inquiries_history(): void
    {
        PropertyInquiry::create([
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
            'provider_id' => $this->provider->id,
            'student_name' => $this->student->name,
            'student_phone' => $this->student->phone,
            'target_move_in_date' => now()->addDays(4),
            'status' => 'new',
            'message' => 'Inquiry for private room',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.inquiries'));
        $response->assertStatus(200);
        $response->assertSeeText('My Inquiries');
        $response->assertSeeText($this->property->title);
        $response->assertSeeText('Inquiry for private room');
    }

    public function test_provider_can_view_received_leads(): void
    {
        PropertyInquiry::create([
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
            'provider_id' => $this->provider->id,
            'student_name' => 'Kunal Verma',
            'student_phone' => '+91 94444 33333',
            'target_move_in_date' => now()->addDays(10),
            'status' => 'new',
            'message' => 'Looking for hostel near Symbiosis',
        ]);

        $response = $this->actingAs($this->provider)->get(route('provider.leads.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Student Inquiries & Leads', false);
        $response->assertSeeText('Kunal Verma');
        $response->assertSeeText('+91 94444 33333');
    }

    public function test_provider_can_update_lead_status_and_notes(): void
    {
        $inquiry = PropertyInquiry::create([
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
            'provider_id' => $this->provider->id,
            'student_name' => 'Pooja Sharma',
            'student_phone' => '+91 95555 22222',
            'target_move_in_date' => now()->addDays(3),
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->provider)->patch(route('provider.leads.update', $inquiry->id), [
            'status' => 'contacted',
            'provider_notes' => 'Spoke on phone, visiting tomorrow at 5 PM',
        ]);

        $response->assertRedirect();
        $this->assertEquals('contacted', $inquiry->fresh()->status);
        $this->assertNotNull($inquiry->fresh()->contacted_at);
        $this->assertEquals('Spoke on phone, visiting tomorrow at 5 PM', $inquiry->fresh()->provider_notes);
    }

    public function test_provider_cannot_update_another_providers_lead(): void
    {
        $inquiry = PropertyInquiry::create([
            'user_id' => $this->student->id,
            'property_id' => $this->property->id,
            'provider_id' => $this->provider->id,
            'student_name' => 'Pooja Sharma',
            'student_phone' => '+91 95555 22222',
            'target_move_in_date' => now()->addDays(3),
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->otherProvider)->patch(route('provider.leads.update', $inquiry->id), [
            'status' => 'closed',
        ]);

        $response->assertStatus(403);
    }
}
