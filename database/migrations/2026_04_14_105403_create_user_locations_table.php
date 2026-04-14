<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('full_address')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();

            $table->boolean('is_available')->default(true);
            $table->boolean('notification_enabled')->default(true);
            $table->decimal('radius_preference_km', 8, 2)->default(20.00);

            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_locations');
    }
};