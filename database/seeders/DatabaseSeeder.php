<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ProviderProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@stayfinder.com'],
            [
                'name' => 'StayFinder Admin',
                'password' => Hash::make('password123'),
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'phone' => '+91 99999 00000',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 2. Verified Provider User
        $provider = User::firstOrCreate(
            ['email' => 'provider@stayfinder.com'],
            [
                'name' => 'Rajesh Kumar',
                'password' => Hash::make('password123'),
                'role' => UserRole::PROVIDER,
                'status' => UserStatus::ACTIVE,
                'phone' => '+91 98765 43210',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        ProviderProfile::updateOrCreate(
            ['user_id' => $provider->id],
            [
                'business_name' => 'Sunrise Student Living & PG',
                'owner_name' => 'Rajesh Kumar',
                'phone' => '+91 98765 43210',
                'whatsapp_number' => '+91 98765 43210',
                'city' => 'Pune',
                'address' => 'Plot 42, Near Symbiosis International, Viman Nagar, Pune, Maharashtra 411014',
                'id_proof_type' => 'aadhaar',
                'is_verified' => true,
                'verified_at' => now(),
                'verification_notes' => 'Physical premise inspection passed and government ID verified.',
                'subscription_tier' => 'premium',
                'response_rate' => 98,
                'avg_response_time' => 'Within 15 mins',
            ]
        );

        // 3. Student User
        $student = User::firstOrCreate(
            ['email' => 'student@stayfinder.com'],
            [
                'name' => 'Aarav Sharma',
                'password' => Hash::make('password123'),
                'role' => UserRole::STUDENT,
                'status' => UserStatus::ACTIVE,
                'phone' => '+91 91234 56789',
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        StudentProfile::updateOrCreate(
            ['user_id' => $student->id],
            [
                'gender' => 'male',
                'college_name' => 'COEP Technological University',
                'course_or_degree' => 'B.Tech Computer Engineering',
                'year_of_study' => '2nd Year',
                'preferred_city' => 'Pune',
                'budget_min' => 6000,
                'budget_max' => 12000,
                'preferred_room_type' => 'double',
                'bio' => 'Looking for a clean, peaceful double sharing PG with WiFi, study table and hygienic food near Shivaji Nagar.',
                'emergency_contact' => '+91 98111 22334',
            ]
        );

        // 4. Seed Properties, Rooms, and Amenities
        $this->call(PropertySeeder::class);

        // 5. Seed Demo Saved Stays for Student
        $properties = \App\Models\Property::take(2)->get();
        foreach ($properties as $prop) {
            \App\Models\SavedProperty::firstOrCreate([
                'user_id' => $student->id,
                'property_id' => $prop->id,
            ]);
        }

        // 6. Seed Demo Inquiries for Provider
        $firstProp = $properties->first();
        if ($firstProp) {
            \App\Models\PropertyInquiry::firstOrCreate(
                [
                    'property_id' => $firstProp->id,
                    'student_phone' => '+91 91234 56789',
                ],
                [
                    'user_id' => $student->id,
                    'provider_id' => $firstProp->user_id,
                    'student_name' => 'Aarav Sharma',
                    'student_email' => 'student@stayfinder.com',
                    'target_move_in_date' => now()->addDays(5),
                    'preferred_room_type' => 'double',
                    'message' => 'Hello Rajesh ji, I am starting my semester at COEP next week. Is a double sharing bed available?',
                    'status' => 'new',
                ]
            );

            \App\Models\PropertyInquiry::firstOrCreate(
                [
                    'property_id' => $firstProp->id,
                    'student_phone' => '+91 98711 22334',
                ],
                [
                    'user_id' => null,
                    'provider_id' => $firstProp->user_id,
                    'student_name' => 'Pooja Verma',
                    'student_email' => 'pooja.v@gmail.com',
                    'target_move_in_date' => now()->addDays(12),
                    'preferred_room_type' => 'single',
                    'message' => 'Hi, what is the gate curfew time on weekends and is WiFi unlimited for video lectures?',
                    'status' => 'contacted',
                    'provider_notes' => 'Spoke on WhatsApp. Scheduled physical room visit on Saturday.',
                    'contacted_at' => now()->subHours(2),
                ]
            );

            // 7. Seed Demo In-App Conversation & Messages
            $conv = \App\Models\Conversation::firstOrCreate(
                [
                    'property_id' => $firstProp->id,
                    'student_id' => $student->id,
                    'provider_id' => $firstProp->user_id,
                ],
                [
                    'last_message_at' => now(),
                ]
            );

            if ($conv->messages()->count() === 0) {
                $conv->messages()->createMany([
                    [
                        'sender_id' => $student->id,
                        'body' => 'Hello Rajesh ji! I saw your PG on StayFinder. Is a double sharing bed with attached bathroom available for next month?',
                        'created_at' => now()->subHours(5),
                    ],
                    [
                        'sender_id' => $provider->id,
                        'body' => 'Hi Aarav! Yes, we have 2 vacant double sharing beds available on the 2nd floor with AC and attached washroom.',
                        'read_at' => now()->subHours(4),
                        'created_at' => now()->subHours(4),
                    ],
                    [
                        'sender_id' => $student->id,
                        'body' => 'That sounds great! Are 3 meals included on Sundays as well?',
                        'created_at' => now()->subHours(3),
                    ],
                    [
                        'sender_id' => $provider->id,
                        'body' => 'Yes, 3 meals are included every day including Sundays (special lunch). You can visit tomorrow at 4 PM to inspect the room.',
                        'created_at' => now()->subHours(1),
                    ],
                ]);
            }

            // 8. Seed Demo Student Review with Sub-ratings & Provider Reply
            \App\Models\Review::updateOrCreate(
                [
                    'property_id' => $firstProp->id,
                    'user_id' => $student->id,
                ],
                [
                    'rating' => 5,
                    'cleanliness_rating' => 5,
                    'food_rating' => 4,
                    'wifi_rating' => 5,
                    'safety_rating' => 5,
                    'behavior_rating' => 5,
                    'review' => 'Lived here for 6 months during my 1st year at COEP. The WiFi is blazing fast (100 Mbps fiber), warden is very helpful and treats students like family. RO water and daily cleaning of rooms are top-notch.',
                    'provider_reply' => 'Thank you Aarav for your kind words! We always strive to provide a quiet, homely study environment for engineering students.',
                    'provider_replied_at' => now()->subDays(2),
                    'is_approved' => true,
                ]
            );

            $firstProp->recalculateRating();
        }

        // 9. Seed Subscription Plans for Providers
        $freePlan = \App\Models\SubscriptionPlan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free Starter',
                'price' => 0.00,
                'billing_interval' => 'monthly',
                'listing_limit' => 1,
                'features' => [
                    '1 Active Student Accommodation Listing',
                    'Standard Campus Proximity Search Visibility',
                    'Direct Student Phone Calls & WhatsApp',
                    'Basic In-App Messaging',
                    'Zero Student Fees Forever',
                ],
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        $proPlan = \App\Models\SubscriptionPlan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro Campus',
                'price' => 499.00,
                'billing_interval' => 'monthly',
                'listing_limit' => 3,
                'features' => [
                    'Up to 3 Active Accommodation Listings',
                    'Verified Student Stay Badge ✓',
                    '2x Priority Search Ranking in City',
                    'Dedicated Leads Management CRM Console',
                    'In-App Chat with Student Read Receipts',
                    'Custom Room Sharing & Rent Variants',
                ],
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $elitePlan = \App\Models\SubscriptionPlan::firstOrCreate(
            ['slug' => 'elite'],
            [
                'name' => 'Campus Elite',
                'price' => 1499.00,
                'billing_interval' => 'monthly',
                'listing_limit' => 15,
                'features' => [
                    'Up to 15 Active Accommodations (Hostel Chains)',
                    'Top Pinned Search Ranking across Campuses',
                    'Featured Gold Highlight Badge',
                    'Instant WhatsApp & SMS Student Lead Notifications',
                    'Multi-Property Vacancy & Room Manager',
                    'Dedicated Account Manager & Listing Audits',
                ],
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        // Assign active pro plan to demo provider
        if ($provider) {
            \App\Models\Subscription::firstOrCreate(
                [
                    'user_id' => $provider->id,
                    'plan_id' => $proPlan->id,
                ],
                [
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addDays(30),
                ]
            );
        }
    }
}
