<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Amenities
        $amenitiesData = [
            ['name' => 'High-Speed WiFi', 'slug' => 'wifi', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'wifi'],
            ['name' => '3 Meals / Daily Food', 'slug' => 'food', 'category' => 'food', 'is_popular' => true, 'icon' => 'utensils'],
            ['name' => 'Air Conditioning (AC)', 'slug' => 'ac', 'category' => 'comfort', 'is_popular' => true, 'icon' => 'snowflake'],
            ['name' => 'Attached Bathroom', 'slug' => 'attached-bathroom', 'category' => 'comfort', 'is_popular' => true, 'icon' => 'bath'],
            ['name' => 'Washing Machine / Laundry', 'slug' => 'laundry', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'washer'],
            ['name' => 'Power Backup / Inverter', 'slug' => 'power-backup', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'bolt'],
            ['name' => '24/7 CCTV & Security Guard', 'slug' => 'cctv-security', 'category' => 'security', 'is_popular' => true, 'icon' => 'shield'],
            ['name' => 'Biometric Gate & Warden', 'slug' => 'biometric-warden', 'category' => 'security', 'is_popular' => false, 'icon' => 'lock'],
            ['name' => 'Dedicated Study Desk & Chair', 'slug' => 'study-table', 'category' => 'comfort', 'is_popular' => true, 'icon' => 'desk'],
            ['name' => 'Hot Water Geyser', 'slug' => 'geyser', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'fire'],
            ['name' => 'Two-Wheeler Parking', 'slug' => 'parking', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'bike'],
            ['name' => 'RO Purified Drinking Water', 'slug' => 'ro-water', 'category' => 'food', 'is_popular' => true, 'icon' => 'droplet'],
            ['name' => 'Refrigerator in Common Area', 'slug' => 'fridge', 'category' => 'food', 'is_popular' => false, 'icon' => 'fridge'],
            ['name' => 'Daily Housekeeping', 'slug' => 'housekeeping', 'category' => 'essentials', 'is_popular' => true, 'icon' => 'sparkles'],
        ];

        $amenityModels = [];
        foreach ($amenitiesData as $item) {
            $amenityModels[$item['slug']] = Amenity::updateOrCreate(['slug' => $item['slug']], $item);
        }

        // Get or create Provider User
        $provider = User::where('role', UserRole::PROVIDER)->first();
        if (! $provider) {
            $provider = User::create([
                'name' => 'Rajesh Kumar (Sunrise Living)',
                'email' => 'provider.seed@stayfinder.com',
                'password' => bcrypt('password123'),
                'role' => UserRole::PROVIDER,
                'status' => \App\Enums\UserStatus::ACTIVE,
                'phone' => '+91 98765 43210',
            ]);
        }

        // 2. Seed Realistic Properties
        $properties = [
            [
                'title' => 'Sunrise Student Living & PG',
                'slug' => 'sunrise-student-living-pg-pune',
                'property_type' => 'pg',
                'gender_preference' => 'male',
                'description' => 'Sunrise Student Living offers premium, spacious student accommodation in the heart of Viman Nagar. Designed specifically for college students and coaching scholars with high-speed 300 Mbps WiFi, separate study desks, hygienic homestyle 3 meals a day, and 24/7 on-site caretaker.',
                'address' => 'Plot 42, Near Symbiosis International, Clover Park, Viman Nagar',
                'locality' => 'Viman Nagar',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411014',
                'latitude' => 18.5679,
                'longitude' => 73.9143,
                'monthly_rent_min' => 7500,
                'monthly_rent_max' => 12000,
                'security_deposit' => 5000,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => true,
                'status' => 'active',
                'food_included' => true,
                'food_details' => 'Nutritious homestyle meals 3 times daily (Breakfast with tea/coffee, Lunch packed for college, wholesome Dinner). Special menu on Sundays. Pure RO drinking water.',
                'gate_closing_time' => '10:30 PM',
                'rules' => [
                    'Smoking and alcohol strictly prohibited inside premises',
                    'Gate closing time is 10:30 PM (permission required for late return)',
                    'Day visitors allowed in reception and common room only',
                    'Quiet study hours after 11:00 PM',
                ],
                'nearby_colleges' => [
                    ['name' => 'Symbiosis International University', 'distance_km' => 0.85],
                    ['name' => 'Pune Institute of Applied Technology', 'distance_km' => 2.1],
                    ['name' => 'Ajeenkya DY Patil University', 'distance_km' => 4.5],
                ],
                'nearby_transport' => [
                    ['name' => 'Ramwadi Metro Station', 'distance_km' => 1.2],
                    ['name' => 'Viman Nagar Bus Stop', 'distance_km' => 0.3],
                    ['name' => 'Pune Airport', 'distance_km' => 2.5],
                ],
                'total_beds' => 24,
                'available_beds' => 4,
                'rating' => 4.8,
                'review_count' => 38,
                'views_count' => 1420,
                'inquiries_count' => 47,
                'amenities' => ['wifi', 'food', 'ac', 'attached-bathroom', 'laundry', 'power-backup', 'cctv-security', 'study-table', 'geyser', 'parking', 'ro-water', 'housekeeping'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Spacious Double Sharing Room with Workstations', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Comfortable Twin Beds & Natural Sunlight', 'is_primary' => false],
                    ['url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Clean Attached Washroom with Hot Water Geyser', 'is_primary' => false],
                    ['url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Common Dining Hall & Lounge Area', 'is_primary' => false],
                ],
                'rooms' => [
                    ['room_type' => 'single', 'title' => 'Private Single Room with Balcony', 'monthly_rent' => 12000, 'security_deposit' => 6000, 'total_capacity' => 1, 'available_capacity' => 1, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => true],
                    ['room_type' => 'double', 'title' => 'AC Double Sharing Room', 'monthly_rent' => 8500, 'security_deposit' => 5000, 'total_capacity' => 2, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => false],
                    ['room_type' => 'triple', 'title' => 'Standard Triple Sharing Room', 'monthly_rent' => 7500, 'security_deposit' => 5000, 'total_capacity' => 3, 'available_capacity' => 1, 'has_attached_bathroom' => true, 'has_ac' => false, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'Scholar Girls Hostel & Residency',
                'slug' => 'scholar-girls-hostel-residency-pune',
                'property_type' => 'hostel',
                'gender_preference' => 'female',
                'description' => 'A trusted, high-security student residence for female scholars and engineering/medical students in Shivajinagar. Features 24/7 lady warden, biometric access gates, dedicated silent study halls, and high-speed fiber internet.',
                'address' => 'Plot 18, Revenue Colony, Near FC Road, Shivajinagar',
                'locality' => 'Shivajinagar',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411005',
                'latitude' => 18.5308,
                'longitude' => 73.8474,
                'monthly_rent_min' => 8500,
                'monthly_rent_max' => 14000,
                'security_deposit' => 8000,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => true,
                'status' => 'active',
                'food_included' => true,
                'food_details' => 'Healthy sattvic vegetarian food prepared by trained chefs. Breakfast, lunch, evening tea & snacks, dinner included.',
                'gate_closing_time' => '10:00 PM',
                'rules' => [
                    'Strict female-only entry inside student floors',
                    'Biometric attendance check before 10:00 PM',
                    'Parent notification system via SMS for late slips',
                    'Warden permission required for night-outs',
                ],
                'nearby_colleges' => [
                    ['name' => 'COEP Technological University', 'distance_km' => 1.1],
                    ['name' => 'Fergusson College (FC Road)', 'distance_km' => 1.8],
                    ['name' => 'Modern College of Engineering', 'distance_km' => 0.9],
                ],
                'nearby_transport' => [
                    ['name' => 'Shivajinagar Metro Station', 'distance_km' => 0.6],
                    ['name' => 'Shivajinagar Railway Station', 'distance_km' => 0.8],
                ],
                'total_beds' => 36,
                'available_beds' => 6,
                'rating' => 4.9,
                'review_count' => 52,
                'views_count' => 2180,
                'inquiries_count' => 63,
                'amenities' => ['wifi', 'food', 'ac', 'attached-bathroom', 'laundry', 'power-backup', 'cctv-security', 'biometric-warden', 'study-table', 'geyser', 'ro-water', 'housekeeping'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Spacious Air-Conditioned Bedroom', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Twin Study Tables & Bookcases', 'is_primary' => false],
                    ['url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Quiet Study Library & Wi-Fi Workspace', 'is_primary' => false],
                ],
                'rooms' => [
                    ['room_type' => 'single', 'title' => 'Deluxe Single Occupancy Room', 'monthly_rent' => 14000, 'security_deposit' => 10000, 'total_capacity' => 1, 'available_capacity' => 1, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => true],
                    ['room_type' => 'double', 'title' => 'Comfort Double Sharing Room', 'monthly_rent' => 9500, 'security_deposit' => 8000, 'total_capacity' => 2, 'available_capacity' => 3, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => false],
                    ['room_type' => 'triple', 'title' => 'Standard Triple Sharing Room', 'monthly_rent' => 8500, 'security_deposit' => 7000, 'total_capacity' => 3, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => false, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'DU North Campus 2BHK Student Share',
                'slug' => 'du-north-campus-2bhk-student-flat-delhi',
                'property_type' => 'flat',
                'gender_preference' => 'any',
                'description' => 'Fully furnished 2BHK flat directly across from DU North Campus gate. Complete with beds, study tables, air conditioners in both rooms, modular kitchen with gas and RO purifier, and high-speed internet.',
                'address' => 'Bungalow Road, Opposite Ramjas College Gate, Kamla Nagar',
                'locality' => 'Kamla Nagar',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110007',
                'latitude' => 28.6833,
                'longitude' => 77.2064,
                'monthly_rent_min' => 6200,
                'monthly_rent_max' => 9000,
                'security_deposit' => 6200,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => false,
                'status' => 'active',
                'food_included' => false,
                'food_details' => 'Full kitchen setup with gas pipe, refrigerator, and microwave. Cook contacts available upon move-in.',
                'gate_closing_time' => 'No Curfew',
                'rules' => [
                    'Independent flat freedom with self-managed keys',
                    'No loud music disturbing residential neighbours after midnight',
                    'Electricity bill split as per sub-meter',
                ],
                'nearby_colleges' => [
                    ['name' => 'Delhi University North Campus', 'distance_km' => 0.6],
                    ['name' => 'Hindu College', 'distance_km' => 0.8],
                    ['name' => 'Hansraj College', 'distance_km' => 0.9],
                    ['name' => 'Kirori Mal College', 'distance_km' => 1.0],
                ],
                'nearby_transport' => [
                    ['name' => 'Vishwa Vidyalaya Metro Station', 'distance_km' => 1.2],
                    ['name' => 'Guru Tegh Bahadur Nagar Metro', 'distance_km' => 1.4],
                ],
                'total_beds' => 4,
                'available_beds' => 2,
                'rating' => 4.6,
                'review_count' => 19,
                'views_count' => 980,
                'inquiries_count' => 29,
                'amenities' => ['wifi', 'ac', 'attached-bathroom', 'laundry', 'power-backup', 'study-table', 'geyser', 'ro-water', 'fridge'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Furnished Student Living & Study Room', 'is_primary' => true],
                    ['url' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Spacious Living Room & Dining Area', 'is_primary' => false],
                ],
                'rooms' => [
                    ['room_type' => 'double', 'title' => 'Master Bedroom Double Share', 'monthly_rent' => 8500, 'security_deposit' => 8500, 'total_capacity' => 2, 'available_capacity' => 1, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => true],
                    ['room_type' => 'double', 'title' => 'Second Bedroom Double Share', 'monthly_rent' => 6200, 'security_deposit' => 6200, 'total_capacity' => 2, 'available_capacity' => 1, 'has_attached_bathroom' => false, 'has_ac' => true, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'Aspirants Coaching Residency & PG',
                'slug' => 'aspirants-coaching-residency-pg-kota',
                'property_type' => 'pg',
                'gender_preference' => 'male',
                'description' => 'Quiet, distraction-free environment tailored exclusively for JEE, NEET, and Olympiad students. Ergonomic study setups, pin-drop silence hours, daily laundry service, and healthy food to keep focus sharp.',
                'address' => 'Sector C, Landmark City, Kunhari',
                'locality' => 'Kunhari',
                'city' => 'Kota',
                'state' => 'Rajasthan',
                'pincode' => '324008',
                'latitude' => 25.1982,
                'longitude' => 75.8342,
                'monthly_rent_min' => 7000,
                'monthly_rent_max' => 11000,
                'security_deposit' => 5000,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => true,
                'status' => 'active',
                'food_included' => true,
                'food_details' => 'Balanced 4 meals a day (Breakfast, Lunch, Evening Snacks with boost/milk, Dinner). Curated for student health.',
                'gate_closing_time' => '9:30 PM',
                'rules' => [
                    'Strict focus on competitive exam preparations',
                    'Daily attendance and progress review with parents',
                    'Curfew strictly enforced at 9:30 PM',
                ],
                'nearby_colleges' => [
                    ['name' => 'Allen Samyak Campus', 'distance_km' => 0.4],
                    ['name' => 'Resonance Eduventures', 'distance_km' => 1.2],
                    ['name' => 'Motion Education Kunhari', 'distance_km' => 0.7],
                ],
                'nearby_transport' => [
                    ['name' => 'Kota Junction Railway Station', 'distance_km' => 6.2],
                ],
                'total_beds' => 30,
                'available_beds' => 5,
                'rating' => 4.7,
                'review_count' => 41,
                'views_count' => 1830,
                'inquiries_count' => 55,
                'amenities' => ['wifi', 'food', 'ac', 'attached-bathroom', 'laundry', 'power-backup', 'cctv-security', 'biometric-warden', 'study-table', 'geyser', 'ro-water', 'housekeeping'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Study Room with Big Bookshelf and Desk', 'is_primary' => true],
                ],
                'rooms' => [
                    ['room_type' => 'single', 'title' => 'Single Silent Study Room', 'monthly_rent' => 11000, 'security_deposit' => 5000, 'total_capacity' => 1, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => false],
                    ['room_type' => 'double', 'title' => 'Double Sharing Room', 'monthly_rent' => 7000, 'security_deposit' => 5000, 'total_capacity' => 2, 'available_capacity' => 3, 'has_attached_bathroom' => true, 'has_ac' => false, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'Koramangala Student Co-Living Space',
                'slug' => 'koramangala-student-coliving-space-bengaluru',
                'property_type' => 'room',
                'gender_preference' => 'any',
                'description' => 'Modern co-living space located in Bengaluru\'s lively Koramangala area, popular among Christ University, design, and tech students. Features high-speed fiber internet, rooftop cafe lounge, and gaming area.',
                'address' => '5th Block, 1st Cross, Near Jyoti Nivas College Road, Koramangala',
                'locality' => 'Koramangala',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560095',
                'latitude' => 12.9352,
                'longitude' => 77.6245,
                'monthly_rent_min' => 9500,
                'monthly_rent_max' => 16000,
                'security_deposit' => 12000,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => true,
                'status' => 'active',
                'food_included' => false,
                'food_details' => 'Community kitchen equipped with induction, microwave, and fridge. Multiple student cafes within 50 meters.',
                'gate_closing_time' => 'No Curfew',
                'rules' => [
                    'Respect co-living peers and common spaces',
                    'Keycard electronic access required for room entry',
                    'Zero tolerance for drug use',
                ],
                'nearby_colleges' => [
                    ['name' => 'Christ University (Main Campus)', 'distance_km' => 1.4],
                    ['name' => 'Jyoti Nivas College (JNC)', 'distance_km' => 0.5],
                    ['name' => 'St. John\'s Medical College', 'distance_km' => 1.8],
                ],
                'nearby_transport' => [
                    ['name' => 'Sony World Junction Bus Stop', 'distance_km' => 0.3],
                    ['name' => 'Silk Board Metro Station', 'distance_km' => 3.2],
                ],
                'total_beds' => 20,
                'available_beds' => 3,
                'rating' => 4.8,
                'review_count' => 27,
                'views_count' => 1640,
                'inquiries_count' => 42,
                'amenities' => ['wifi', 'ac', 'attached-bathroom', 'laundry', 'power-backup', 'cctv-security', 'study-table', 'geyser', 'parking', 'fridge', 'housekeeping'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Chic Studio Bedroom with Workstation', 'is_primary' => true],
                ],
                'rooms' => [
                    ['room_type' => 'single', 'title' => 'Private Studio Room', 'monthly_rent' => 16000, 'security_deposit' => 16000, 'total_capacity' => 1, 'available_capacity' => 1, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => true],
                    ['room_type' => 'double', 'title' => 'Twin Sharing Room', 'monthly_rent' => 9500, 'security_deposit' => 9500, 'total_capacity' => 2, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => true, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'Kothrud Scholastic Girls PG',
                'slug' => 'kothrud-scholastic-girls-pg-pune',
                'property_type' => 'pg',
                'gender_preference' => 'female',
                'description' => 'Homely, peaceful paying guest accommodation for girls attending MIT-WPU, Bharati Vidyapeeth, and Cummins College. Includes daily home-cooked Maharashtrian & North Indian food, washing machine, and high security.',
                'address' => 'Near MIT Circle, Paud Road, Kothrud',
                'locality' => 'Kothrud',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411038',
                'latitude' => 18.5074,
                'longitude' => 73.8077,
                'monthly_rent_min' => 6500,
                'monthly_rent_max' => 9500,
                'security_deposit' => 6000,
                'notice_period_days' => 30,
                'is_verified' => true,
                'is_featured' => false,
                'status' => 'active',
                'food_included' => true,
                'food_details' => '2 Meals daily (Breakfast & Dinner on weekdays, Lunch on weekends).',
                'gate_closing_time' => '10:00 PM',
                'rules' => [
                    'Girls-only premises',
                    'Gate locked at 10:00 PM',
                    'Cleanliness must be maintained in rooms',
                ],
                'nearby_colleges' => [
                    ['name' => 'MIT World Peace University (MIT-WPU)', 'distance_km' => 0.75],
                    ['name' => 'Cummins College of Engineering', 'distance_km' => 2.4],
                    ['name' => 'Bharati Vidyapeeth Kothrud', 'distance_km' => 1.5],
                ],
                'nearby_transport' => [
                    ['name' => 'Vanaz Metro Station', 'distance_km' => 0.9],
                    ['name' => 'Ideal Colony Metro Station', 'distance_km' => 1.1],
                ],
                'total_beds' => 18,
                'available_beds' => 4,
                'rating' => 4.7,
                'review_count' => 24,
                'views_count' => 1120,
                'inquiries_count' => 33,
                'amenities' => ['wifi', 'food', 'attached-bathroom', 'laundry', 'power-backup', 'cctv-security', 'study-table', 'geyser', 'ro-water', 'housekeeping'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Double Room with Individual Wardrobes', 'is_primary' => true],
                ],
                'rooms' => [
                    ['room_type' => 'double', 'title' => 'Double Sharing Room', 'monthly_rent' => 8500, 'security_deposit' => 6000, 'total_capacity' => 2, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => false, 'has_balcony' => false],
                    ['room_type' => 'triple', 'title' => 'Triple Sharing Room', 'monthly_rent' => 6500, 'security_deposit' => 6000, 'total_capacity' => 3, 'available_capacity' => 2, 'has_attached_bathroom' => false, 'has_ac' => false, 'has_balcony' => false],
                ],
            ],

            [
                'title' => 'Shri Sai Student Lodge & Budget Dormitory',
                'slug' => 'shri-sai-student-lodge-budget-dormitory-pune',
                'property_type' => 'lodge',
                'gender_preference' => 'male',
                'description' => 'Ultra-affordable student lodge and dormitory specially designed for diploma students, coaching aspirants, and exam candidates. Provides clean bunk beds, study hall with high-speed WiFi, RO drinking water, and hot geyser water.',
                'address' => 'Near Shivaji Nagar Bus Stand, J.M. Road, Shivaji Nagar',
                'locality' => 'Shivaji Nagar',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411005',
                'latitude' => 18.5314,
                'longitude' => 73.8446,
                'monthly_rent_min' => 1000,
                'monthly_rent_max' => 2500,
                'security_deposit' => 1000,
                'notice_period_days' => 15,
                'is_verified' => true,
                'is_featured' => false,
                'status' => 'active',
                'food_included' => false,
                'food_details' => 'Affordable student mess and tiffin center right next door starting at ₹60/thali.',
                'gate_closing_time' => '10:30 PM',
                'rules' => [
                    'Quiet hours after 11:00 PM for study',
                    'Smoking and alcohol strictly prohibited',
                    'Valid student ID required at check-in',
                ],
                'nearby_colleges' => [
                    ['name' => 'COEP Technological University', 'distance_km' => 0.5],
                    ['name' => 'Fergusson College (FC Road)', 'distance_km' => 1.2],
                    ['name' => 'Modern College of Arts, Science and Commerce', 'distance_km' => 0.8],
                ],
                'nearby_transport' => [
                    ['name' => 'Shivaji Nagar Railway Station & Metro', 'distance_km' => 0.3],
                ],
                'total_beds' => 24,
                'available_beds' => 6,
                'rating' => 4.5,
                'review_count' => 18,
                'views_count' => 1420,
                'inquiries_count' => 38,
                'amenities' => ['wifi', 'study-table', 'geyser', 'ro-water', 'cctv-security', 'power-backup'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80', 'caption' => 'Clean Budget Dormitory Hall with Study Desks', 'is_primary' => true],
                ],
                'rooms' => [
                    ['room_type' => 'four_plus', 'title' => 'Dormitory Bunk Bed', 'monthly_rent' => 1000, 'security_deposit' => 1000, 'total_capacity' => 6, 'available_capacity' => 4, 'has_attached_bathroom' => false, 'has_ac' => false, 'has_balcony' => false],
                    ['room_type' => 'triple', 'title' => 'Triple Sharing Economy Room', 'monthly_rent' => 2200, 'security_deposit' => 1500, 'total_capacity' => 3, 'available_capacity' => 2, 'has_attached_bathroom' => true, 'has_ac' => false, 'has_balcony' => false],
                ],
            ],
        ];

        foreach ($properties as $propData) {
            $amenitySlugs = $propData['amenities'] ?? [];
            $imagesData = $propData['images'] ?? [];
            $roomsData = $propData['rooms'] ?? [];

            unset($propData['amenities'], $propData['images'], $propData['rooms']);

            $propData['user_id'] = $provider->id;

            $property = Property::updateOrCreate(
                ['slug' => $propData['slug']],
                $propData
            );

            // Sync amenities
            $amenityIds = [];
            foreach ($amenitySlugs as $slug) {
                if (isset($amenityModels[$slug])) {
                    $amenityIds[] = $amenityModels[$slug]->id;
                }
            }
            $property->amenities()->sync($amenityIds);

            // Create images if none exist
            if ($property->images()->count() === 0) {
                foreach ($imagesData as $img) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $img['url'],
                        'caption' => $img['caption'] ?? null,
                        'is_primary' => $img['is_primary'] ?? false,
                    ]);
                }
            }

            // Create rooms if none exist
            if ($property->rooms()->count() === 0) {
                foreach ($roomsData as $room) {
                    $room['property_id'] = $property->id;
                    PropertyRoom::create($room);
                }
            }
        }
    }
}
