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
        Schema::create('property_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();

            $table->string('student_name');
            $table->string('student_phone');
            $table->string('student_email')->nullable();
            $table->date('target_move_in_date');
            $table->string('preferred_room_type')->nullable(); // single, double, triple, any
            $table->text('message')->nullable();
            
            // Status lifecycle: new, contacted, visit_scheduled, converted, closed
            $table->string('status')->default('new')->index();
            $table->text('provider_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();

            $table->timestamps();

            $table->index(['provider_id', 'status']);
            $table->index(['property_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_inquiries');
    }
};
