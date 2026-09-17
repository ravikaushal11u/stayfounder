<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('room_type')->index(); // single, double, triple, four_plus
            $table->string('title'); // e.g. "AC Double Sharing with Study Table"
            $table->unsignedInteger('monthly_rent');
            $table->unsignedInteger('security_deposit')->default(0);
            $table->unsignedSmallInteger('total_capacity')->default(1);
            $table->unsignedSmallInteger('available_capacity')->default(1);
            $table->boolean('has_attached_bathroom')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('has_balcony')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_rooms');
    }
};
