<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function foreignKeyExists(string $table, string $column): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $database = DB::getDatabaseName();

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Rename cow_conditions table to animal_conditions safely
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('animal_conditions')) {
            if (Schema::hasTable('cow_conditions')) {
                Schema::rename('cow_conditions', 'animal_conditions');
            } else {
                Schema::create('animal_conditions', function (Blueprint $table) {
                    $table->id();

                    $table->unsignedBigInteger('report_type_id')->nullable();

                    $table->string('name', 150);
                    $table->string('slug', 180)->unique();

                    $table->enum('severity_level', [
                        'low',
                        'medium',
                        'high',
                        'critical',
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
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Add report_type_id to animal_conditions if missing
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('animal_conditions') && !Schema::hasColumn('animal_conditions', 'report_type_id')) {
            Schema::table('animal_conditions', function (Blueprint $table) {
                $table->unsignedBigInteger('report_type_id')->nullable()->after('id');
                $table->index(['report_type_id', 'status']);
            });
        }

        if (
            Schema::hasTable('animal_conditions') &&
            Schema::hasTable('report_types') &&
            Schema::hasColumn('animal_conditions', 'report_type_id') &&
            !$this->foreignKeyExists('animal_conditions', 'report_type_id')
        ) {
            Schema::table('animal_conditions', function (Blueprint $table) {
                $table->foreign('report_type_id')
                    ->references('id')
                    ->on('report_types')
                    ->nullOnDelete();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Add animal_type_id and report_type_id to emergency_cases first
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('emergency_cases', 'animal_type_id')) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->unsignedBigInteger('animal_type_id')->nullable()->after('reporter_id');
            });
        }

        if (!Schema::hasColumn('emergency_cases', 'report_type_id')) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->unsignedBigInteger('report_type_id')->nullable()->after('animal_type_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Rename old cow_condition_id column if it exists
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasColumn('emergency_cases', 'cow_condition_id') &&
            !Schema::hasColumn('emergency_cases', 'animal_condition_id')
        ) {
            $this->dropForeignIfExists('emergency_cases', 'cow_condition_id');

            DB::statement("
                ALTER TABLE `emergency_cases`
                CHANGE `cow_condition_id` `animal_condition_id` BIGINT UNSIGNED NULL
            ");
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Add animal_condition_id if missing
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->unsignedBigInteger('animal_condition_id')->nullable()->after('report_type_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Add foreign keys only if related tables exist
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasTable('animal_types') &&
            Schema::hasColumn('emergency_cases', 'animal_type_id') &&
            !$this->foreignKeyExists('emergency_cases', 'animal_type_id')
        ) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->foreign('animal_type_id')
                    ->references('id')
                    ->on('animal_types')
                    ->nullOnDelete();
            });
        }

        if (
            Schema::hasTable('report_types') &&
            Schema::hasColumn('emergency_cases', 'report_type_id') &&
            !$this->foreignKeyExists('emergency_cases', 'report_type_id')
        ) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->foreign('report_type_id')
                    ->references('id')
                    ->on('report_types')
                    ->nullOnDelete();
            });
        }

        if (
            Schema::hasTable('animal_conditions') &&
            Schema::hasColumn('emergency_cases', 'animal_condition_id') &&
            !$this->foreignKeyExists('emergency_cases', 'animal_condition_id')
        ) {
            Schema::table('emergency_cases', function (Blueprint $table) {
                $table->foreign('animal_condition_id')
                    ->references('id')
                    ->on('animal_conditions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
            $this->dropForeignIfExists('emergency_cases', 'animal_condition_id');
        }

        if (Schema::hasColumn('emergency_cases', 'report_type_id')) {
            $this->dropForeignIfExists('emergency_cases', 'report_type_id');
        }

        if (Schema::hasColumn('emergency_cases', 'animal_type_id')) {
            $this->dropForeignIfExists('emergency_cases', 'animal_type_id');
        }

        Schema::table('emergency_cases', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
                DB::statement("
                    ALTER TABLE `emergency_cases`
                    CHANGE `animal_condition_id` `cow_condition_id` BIGINT UNSIGNED NULL
                ");
            }

            if (Schema::hasColumn('emergency_cases', 'report_type_id')) {
                $table->dropColumn('report_type_id');
            }

            if (Schema::hasColumn('emergency_cases', 'animal_type_id')) {
                $table->dropColumn('animal_type_id');
            }
        });

        if (Schema::hasTable('animal_conditions') && !Schema::hasTable('cow_conditions')) {
            Schema::rename('animal_conditions', 'cow_conditions');
        }
    }
};