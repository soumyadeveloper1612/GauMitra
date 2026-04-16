<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_case_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emergency_case_id')->constrained('emergency_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('assignment_role', 50)->nullable(); // responder, volunteer, driver, vet
            $table->string('status', 50)->default('assigned'); // assigned, accepted, rejected, reached, completed
            $table->decimal('distance_km', 8, 2)->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reached_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('emergency_case_id');
            $table->index('user_id');
            $table->index('assignment_role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_assignments');
    }
};