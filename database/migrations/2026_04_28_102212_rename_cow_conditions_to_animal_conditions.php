<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function dropForeignIfExists(string $tableName, string $columnName): void
    {
        $database = DB::getDatabaseName();

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $columnName)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraint}`");
        }
    }

    public function up(): void
    {
        if (Schema::hasColumn('emergency_cases', 'cow_condition_id')) {
            $this->dropForeignIfExists('emergency_cases', 'cow_condition_id');
        }

        if (!Schema::hasTable('animal_conditions')) {
            if (Schema::hasTable('cow_conditions')) {
                Schema::rename('cow_conditions', 'animal_conditions');
            } else {
                Schema::create('animal_conditions', function (Blueprint $table) {
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
        }

        Schema::table('emergency_cases', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_cases', 'cow_condition_id') && !Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
                $table->renameColumn('cow_condition_id', 'animal_condition_id');
            }

            if (!Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
                $table->foreignId('animal_condition_id')
                    ->nullable()
                    ->after('report_type_id')
                    ->constrained('animal_conditions')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('emergency_cases', 'animal_condition_id')) {
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

        Schema::table('emergency_cases', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_cases', 'animal_condition_id') && !Schema::hasColumn('emergency_cases', 'cow_condition_id')) {
                $table->renameColumn('animal_condition_id', 'cow_condition_id');
            }
        });

        if (Schema::hasTable('animal_conditions') && !Schema::hasTable('cow_conditions')) {
            Schema::rename('animal_conditions', 'cow_conditions');
        }
    }
};