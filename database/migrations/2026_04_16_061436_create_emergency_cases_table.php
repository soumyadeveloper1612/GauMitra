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

            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('current_handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_case_id')->nullable()->constrained('emergency_cases')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('case_type', 100);
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('severity', 20)->default('medium');
            $table->integer('cattle_count')->nullable()->default(1);

            $table->string('contact_number', 20)->nullable();
            $table->string('vehicle_number', 50)->nullable();
            $table->text('vehicle_details')->nullable();

            $table->text('full_address')->nullable();
            $table->string('district', 150)->nullable();
            $table->string('state', 150)->nullable();
            $table->string('pincode', 20)->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('status', 50)->default('reported');
            $table->boolean('is_duplicate')->default(false);

            $table->decimal('notified_radius_km', 8, 2)->nullable();
            $table->unsignedInteger('escalation_level')->default(0);

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

            $table->index('case_uid');
            $table->index('reporter_id');
            $table->index('current_handler_id');
            $table->index('parent_case_id');
            $table->index('closed_by');
            $table->index('case_type');
            $table->index('severity');
            $table->index('status');
            $table->index(['latitude', 'longitude']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_cases');
    }
};