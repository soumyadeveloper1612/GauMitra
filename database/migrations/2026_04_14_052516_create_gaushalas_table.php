<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaushalas', function (Blueprint $table) {
            $table->id();
            $table->string('gaushala_name');
            $table->string('owner_manager_name');
            $table->string('mobile_number', 15);
            $table->string('alternate_number', 15)->nullable();
            $table->text('full_address');
            $table->string('district', 150);
            $table->string('state', 150);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaushalas');
    }
};