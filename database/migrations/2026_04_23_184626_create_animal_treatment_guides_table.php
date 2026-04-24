<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_treatment_guides', function (Blueprint $table) {
            $table->id();
            $table->string('animal_type')->default('cow');
            $table->string('case_type'); // accident, fever, wound, fracture, infection etc
            $table->string('condition_name');
            $table->text('symptoms')->nullable();
            $table->longText('first_aid_steps')->nullable();
            $table->longText('medicines')->nullable();
            $table->text('dosage')->nullable();
            $table->longText('treatment_steps')->nullable();
            $table->longText('recovery_steps')->nullable();
            $table->longText('precautions')->nullable();
            $table->text('vet_contact_note')->nullable();
            $table->enum('priority', ['normal', 'emergency'])->default('normal');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_treatment_guides');
    }
};