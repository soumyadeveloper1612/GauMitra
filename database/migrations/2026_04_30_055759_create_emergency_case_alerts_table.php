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

            $table->foreignId('emergency_case_id')
                ->constrained('emergency_cases')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('fcm_token')->nullable();
            $table->string('platform')->nullable();
            $table->string('area_name')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();

            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])
                ->default('pending');

            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

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