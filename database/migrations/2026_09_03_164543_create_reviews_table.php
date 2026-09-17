<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('rating'); // 1 to 5 overall
            $table->unsignedTinyInteger('cleanliness_rating')->nullable(); // 1 to 5
            $table->unsignedTinyInteger('food_rating')->nullable(); // 1 to 5
            $table->unsignedTinyInteger('wifi_rating')->nullable(); // 1 to 5
            $table->unsignedTinyInteger('safety_rating')->nullable(); // 1 to 5
            $table->unsignedTinyInteger('behavior_rating')->nullable(); // 1 to 5

            $table->text('review');
            $table->text('provider_reply')->nullable();
            $table->timestamp('provider_replied_at')->nullable();
            $table->boolean('is_approved')->default(true);

            $table->timestamps();

            // A student can review a property once
            $table->unique(['property_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
