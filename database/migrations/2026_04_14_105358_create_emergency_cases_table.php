<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_uid')->unique();

            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_case_id')->nullable()->constrained('emergency_cases')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('case_type'); // accident, injured_cattle, illegal_transport, abandoned_cattle
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->unsignedInteger('cattle_count')->nullable();

            $table->string('contact_number', 20)->nullable();
            $table->string('vehicle_number')->nullable();
            $table->text('vehicle_details')->nullable();

            $table->string('full_address')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 20)->nullable();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('status')->default('reported');
            $table->boolean('is_duplicate')->default(false);
            $table->decimal('notified_radius_km', 5, 2)->default(20.00);
            $table->unsignedTinyInteger('escalation_level')->default(0);

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('en_route_at')->nullable();
            $table->timestamp('reached_at')->nullable();
            $table->timestamp('rescue_started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->text('resolution_notes')->nullable();
            $table->text('false_report_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'case_type']);
            $table->index(['reporter_id']);
            $table->index(['current_handler_id']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_cases');
    }
};