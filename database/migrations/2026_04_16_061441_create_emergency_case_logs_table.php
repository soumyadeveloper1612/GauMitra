<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_case_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emergency_case_id')->constrained('emergency_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action', 100);
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();

            $table->text('notes')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index('emergency_case_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('old_status');
            $table->index('new_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_logs');
    }
};