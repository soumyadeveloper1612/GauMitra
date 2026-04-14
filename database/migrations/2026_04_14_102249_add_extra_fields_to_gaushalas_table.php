<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaushalas', function (Blueprint $table) {
            $table->unsignedInteger('total_capacity')->default(0)->after('state');
            $table->unsignedInteger('available_capacity')->default(0)->after('total_capacity');

            $table->boolean('rescue_vehicle')->default(false)->after('available_capacity');
            $table->boolean('doctor')->default(false)->after('rescue_vehicle');
            $table->boolean('food_support')->default(false)->after('doctor');
            $table->boolean('temporary_shelter')->default(false)->after('food_support');

            $table->string('gaushala_photo')->nullable()->after('temporary_shelter');
            $table->string('registration_proof')->nullable()->after('gaushala_photo');

            $table->string('working_hours')->nullable()->after('registration_proof');
            $table->enum('emergency_availability', ['yes', 'no'])->default('no')->after('working_hours');
        });
    }

    public function down(): void
    {
        Schema::table('gaushalas', function (Blueprint $table) {
            $table->dropColumn([
                'total_capacity',
                'available_capacity',
                'rescue_vehicle',
                'doctor',
                'food_support',
                'temporary_shelter',
                'gaushala_photo',
                'registration_proof',
                'working_hours',
                'emergency_availability',
            ]);
        });
    }
};