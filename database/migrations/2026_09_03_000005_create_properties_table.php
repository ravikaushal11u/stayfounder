<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('property_type')->index(); // pg, hostel, room, flat, lodge
            $table->string('gender_preference')->default('any')->index(); // male, female, any
            $table->longText('description')->nullable();
            
            // Location
            $table->string('address');
            $table->string('locality')->index();
            $table->string('city')->index();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->index();
            $table->decimal('longitude', 11, 8)->nullable()->index();

            // Pricing & Terms
            $table->unsignedInteger('monthly_rent_min')->index();
            $table->unsignedInteger('monthly_rent_max')->nullable()->index();
            $table->unsignedInteger('security_deposit')->default(0);
            $table->unsignedSmallInteger('notice_period_days')->default(30);

            // Badges & Status
            $table->boolean('is_verified')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('status')->default('active')->index(); // active, draft, paused

            // Student specific details
            $table->boolean('food_included')->default(false)->index();
            $table->text('food_details')->nullable();
            $table->string('gate_closing_time')->nullable(); // e.g. "10:30 PM", "No Curfew"
            $table->json('rules')->nullable();
            $table->json('nearby_colleges')->nullable(); // [{"name": "COEP", "distance_km": 1.2}]
            $table->json('nearby_transport')->nullable(); // metro, bus stop

            // Occupancy
            $table->unsignedSmallInteger('total_beds')->default(1);
            $table->unsignedSmallInteger('available_beds')->default(1)->index();

            // Metrics
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('inquiries_count')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
