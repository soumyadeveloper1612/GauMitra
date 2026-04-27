<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cow_conditions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_type_id')
                ->nullable()
                ->constrained('report_types')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('slug', 180)->unique();

            $table->enum('severity_level', [
                'low',
                'medium',
                'high',
                'critical'
            ])->default('medium');

            $table->string('icon_class', 100)->nullable();
            $table->string('color_code', 20)->default('#b45309');

            $table->text('symptoms')->nullable();
            $table->text('first_aid_steps')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');

            $table->timestamps();

            $table->index(['report_type_id', 'status']);
            $table->index(['severity_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cow_conditions');
    }
};