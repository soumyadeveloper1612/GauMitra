<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaushalas', function (Blueprint $table) {
            if (!Schema::hasColumn('gaushalas', 'total_capacity')) {
                $table->unsignedInteger('total_capacity')->default(0)->after('state');
            }

            if (!Schema::hasColumn('gaushalas', 'available_capacity')) {
                $table->unsignedInteger('available_capacity')->default(0)->after('total_capacity');
            }

            if (!Schema::hasColumn('gaushalas', 'rescue_vehicle')) {
                $table->boolean('rescue_vehicle')->default(false)->after('available_capacity');
            }

            if (!Schema::hasColumn('gaushalas', 'doctor')) {
                $table->boolean('doctor')->default(false)->after('rescue_vehicle');
            }

            if (!Schema::hasColumn('gaushalas', 'food_support')) {
                $table->boolean('food_support')->default(false)->after('doctor');
            }

            if (!Schema::hasColumn('gaushalas', 'temporary_shelter')) {
                $table->boolean('temporary_shelter')->default(false)->after('food_support');
            }

            if (!Schema::hasColumn('gaushalas', 'gaushala_photo')) {
                $table->string('gaushala_photo')->nullable()->after('temporary_shelter');
            }

            if (!Schema::hasColumn('gaushalas', 'registration_proof')) {
                $table->string('registration_proof')->nullable()->after('gaushala_photo');
            }

            if (!Schema::hasColumn('gaushalas', 'working_hours')) {
                $table->string('working_hours')->nullable()->after('registration_proof');
            }

            if (!Schema::hasColumn('gaushalas', 'emergency_availability')) {
                $table->enum('emergency_availability', ['yes', 'no'])->default('no')->after('working_hours');
            }

            if (!Schema::hasColumn('gaushalas', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('emergency_availability');
            }
        });

        if (!Schema::hasTable('gaushala_members')) {
            Schema::create('gaushala_members', function (Blueprint $table) {
                $table->id();

                $table->foreignId('gaushala_id')
                    ->constrained('gaushalas')
                    ->cascadeOnDelete();

                $table->string('member_name', 150);
                $table->string('member_phone', 20);
                $table->enum('status', ['active', 'inactive'])->default('active');

                $table->timestamps();

                $table->index(['gaushala_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gaushala_members');

        Schema::table('gaushalas', function (Blueprint $table) {
            $columns = [
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
                'status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('gaushalas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};