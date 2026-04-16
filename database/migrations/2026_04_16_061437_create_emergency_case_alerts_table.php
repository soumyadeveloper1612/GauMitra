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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('device_token_id')->nullable()->constrained('device_tokens')->nullOnDelete();

            $table->string('notification_type', 50)->nullable(); // push, sms, call
            $table->decimal('radius_km', 8, 2)->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();

            $table->string('status', 50)->default('pending'); // pending, sent, failed, seen, responded
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->string('response', 50)->nullable(); // accepted, rejected, no_response
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('emergency_case_id');
            $table->index('user_id');
            $table->index('device_token_id');
            $table->index('status');
            $table->index('response');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_alerts');
    }
};