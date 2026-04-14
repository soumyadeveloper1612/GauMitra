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

            $table->string('assignment_role')->default('support_handler'); // primary_handler, support_handler
            $table->string('status')->default('invited'); // invited, accepted, rejected, on_the_way, reached, completed, cancelled
            $table->decimal('distance_km', 8, 2)->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reached_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['emergency_case_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_assignments');
    }
};