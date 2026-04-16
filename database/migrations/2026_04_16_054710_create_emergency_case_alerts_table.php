<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_case_alerts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emergency_case_id')->constrained('emergency_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // If device_tokens table exists
            $table->foreignId('device_token_id')->nullable()->constrained('device_tokens')->nullOnDelete();

            $table->string('notification_type', 50)->default('push');
            $table->decimal('radius_km', 5, 2)->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();

            $table->string('status', 50)->default('pending')->index();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->string('response', 50)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['emergency_case_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_alerts');
    }
};