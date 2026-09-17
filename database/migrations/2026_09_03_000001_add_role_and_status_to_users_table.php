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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('email')->index();
            $table->string('status')->default('active')->after('role')->index();
            $table->string('phone')->nullable()->after('status');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('phone_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'phone_verified_at', 'avatar']);
        });
    }
};
