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
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();

            $table->string('document_type'); // aadhaar, electricity_bill, trade_license, property_tax
            $table->string('document_path');
            $table->text('notes')->nullable();

            // Status: pending, approved, rejected
            $table->string('status')->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        Schema::create('safety_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('reporter_name')->nullable();
            $table->string('reporter_phone')->nullable();
            
            // Reasons: advance_payment_scam, fake_photos, incorrect_pricing, unsafe_environment, already_full, other
            $table->string('reason');
            $table->text('description');

            // Status: pending, investigating, action_taken, dismissed
            $table->string('status')->default('pending')->index();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_reports');
        Schema::dropIfExists('verification_requests');
    }
};
