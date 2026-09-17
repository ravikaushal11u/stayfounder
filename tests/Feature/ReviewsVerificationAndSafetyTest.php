<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewsVerificationAndSafetyTest extends TestCase
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
            'name' => 'Kavya Sharma',
            'email' => 'kavya@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
            'phone' => '+91 99112 23344',
        ]);

        $this->otherProvider = User::create([
            'name' => 'Other Provider',
            'email' => 'otherprovider@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::PROVIDER,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->post(route('reviews.store', $this->property->id), [
            'rating' => 5,
            'review' => 'Great place for college students.',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_submit_review_and_recalculates_rating(): void
    {
        $response = $this->actingAs($this->student)->post(route('reviews.store', $this->property->id), [
            'rating' => 5,
            'cleanliness_rating' => 5,
            'food_rating' => 4,
            'wifi_rating' => 5,
            'safety_rating' => 5,
            'behavior_rating' => 4,
            'review' => 'Amazing place near Symbiosis! Clean rooms, fast WiFi, and good food.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'property_id' => $this->property->id,
            'user_id' => $this->student->id,
            'rating' => 5,
        ]);

        $this->property->refresh();
        $this->assertEquals(5.0, $this->property->rating);
        $this->assertEquals(1, $this->property->review_count);
    }

    public function test_provider_cannot_review_their_own_property(): void
    {
        $response = $this->actingAs($this->provider)->post(route('reviews.store', $this->property->id), [
            'rating' => 5,
            'review' => 'This is my own property and it is the best.',
        ]);

        $response->assertSessionHas('warning');
        $this->assertDatabaseMissing('reviews', [
            'property_id' => $this->property->id,
            'user_id' => $this->provider->id,
        ]);
    }

    public function test_provider_can_reply_to_student_review(): void
    {
        $review = Review::create([
            'property_id' => $this->property->id,
            'user_id' => $this->student->id,
            'rating' => 4,
            'review' => 'Rooms are clean and spacious. Food is decent.',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->provider)->post(route('reviews.reply', $review->id), [
            'provider_reply' => 'Thank you Kavya for staying with us! We have also upgraded our dinner menu this week.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'provider_reply' => 'Thank you Kavya for staying with us! We have also upgraded our dinner menu this week.',
        ]);
    }

    public function test_other_user_cannot_reply_to_review(): void
    {
        $review = Review::create([
            'property_id' => $this->property->id,
            'user_id' => $this->student->id,
            'rating' => 4,
            'review' => 'Rooms are clean and spacious.',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->otherProvider)->post(route('reviews.reply', $review->id), [
            'provider_reply' => 'Unauthorized reply attempt.',
        ]);

        $response->assertStatus(403);
    }

    public function test_provider_can_view_verification_console(): void
    {
        $response = $this->actingAs($this->provider)->get(route('provider.verification.create'));
        $response->assertStatus(200);
        $response->assertSeeText('Provider Verification');
        $response->assertSeeText('Why get verified on StayFinder?');
    }

    public function test_provider_can_submit_verification_request_with_document(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('electricity_bill.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->provider)->post(route('provider.verification.store'), [
            'document_type' => 'electricity_bill',
            'document' => $file,
            'notes' => 'Commercial meter electricity bill in owner name.',
            'property_id' => $this->property->id,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('verification_requests', [
            'user_id' => $this->provider->id,
            'property_id' => $this->property->id,
            'document_type' => 'electricity_bill',
            'status' => 'pending',
        ]);
    }

    public function test_student_or_guest_can_submit_safety_scam_report(): void
    {
        $response = $this->postJson(route('properties.report', $this->property->id), [
            'reason' => 'advance_payment_scam',
            'description' => 'Owner asked for ₹2,000 via UPI before even allowing room inspection.',
            'reporter_name' => 'Alert Student',
            'reporter_phone' => '+91 98888 77777',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('safety_reports', [
            'property_id' => $this->property->id,
            'reason' => 'advance_payment_scam',
            'reporter_name' => 'Alert Student',
            'status' => 'pending',
        ]);
    }
}
