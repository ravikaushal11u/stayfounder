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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('whatsapp_number')->nullable();
            $table->string('city')->index();
            $table->text('address')->nullable();
            $table->string('id_proof_type')->nullable(); // aadhaar, pan, trade_license
            $table->string('id_proof_path')->nullable();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->string('subscription_tier')->default('free')->index(); // free, premium, premium_plus
            $table->timestamp('subscription_expires_at')->nullable();
            $table->unsignedTinyInteger('response_rate')->default(100);
            $table->string('avg_response_time')->default('Within 1 hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
