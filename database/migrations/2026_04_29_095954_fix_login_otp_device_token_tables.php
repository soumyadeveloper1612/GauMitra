<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | login_otps table fix
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('login_otps')) {
            Schema::create('login_otps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('mobile', 20);
                $table->string('platform', 30)->nullable();
                $table->string('device_id', 191)->nullable();
                $table->string('purpose', 50)->default('login');
                $table->string('otp_hash');
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->boolean('is_used')->default(false);
                $table->integer('attempts')->default(0);
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index('mobile');
                $table->index(['mobile', 'is_used']);
            });
        } else {
            Schema::table('login_otps', function (Blueprint $table) {
                if (!Schema::hasColumn('login_otps', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id');
                }

                if (!Schema::hasColumn('login_otps', 'platform')) {
                    $table->string('platform', 30)->nullable()->after('mobile');
                }

                if (!Schema::hasColumn('login_otps', 'device_id')) {
                    $table->string('device_id', 191)->nullable()->after('platform');
                }

                if (!Schema::hasColumn('login_otps', 'purpose')) {
                    $table->string('purpose', 50)->default('login')->after('device_id');
                }

                if (!Schema::hasColumn('login_otps', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable();
                }

                if (!Schema::hasColumn('login_otps', 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
            });

            // MySQL / MariaDB permanent nullable/default fixes
            try {
                DB::statement("ALTER TABLE login_otps MODIFY user_id BIGINT UNSIGNED NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY platform VARCHAR(30) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY device_id VARCHAR(191) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY purpose VARCHAR(50) NOT NULL DEFAULT 'login'");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY is_used TINYINT(1) NOT NULL DEFAULT 0");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY attempts INT NOT NULL DEFAULT 0");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY ip_address VARCHAR(45) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE login_otps MODIFY user_agent TEXT NULL");
            } catch (\Throwable $e) {}
        }

        /*
        |--------------------------------------------------------------------------
        | device_tokens table fix
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('device_tokens')) {
            Schema::create('device_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('platform', 30)->nullable();
                $table->string('device_id', 191)->nullable();
                $table->text('fcm_token')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_used_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index(['user_id', 'platform']);
            });
        } else {
            Schema::table('device_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('device_tokens', 'platform')) {
                    $table->string('platform', 30)->nullable()->after('user_id');
                }

                if (!Schema::hasColumn('device_tokens', 'device_id')) {
                    $table->string('device_id', 191)->nullable()->after('platform');
                }

                if (!Schema::hasColumn('device_tokens', 'fcm_token')) {
                    $table->text('fcm_token')->nullable()->after('device_id');
                }

                if (!Schema::hasColumn('device_tokens', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('fcm_token');
                }

                if (!Schema::hasColumn('device_tokens', 'last_used_at')) {
                    $table->timestamp('last_used_at')->nullable()->after('is_active');
                }

                if (!Schema::hasColumn('device_tokens', 'ip_address')) {
                    $table->string('ip_address', 45)->nullable();
                }

                if (!Schema::hasColumn('device_tokens', 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
            });

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY platform VARCHAR(30) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY device_id VARCHAR(191) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY fcm_token TEXT NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY is_active TINYINT(1) NOT NULL DEFAULT 1");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY last_used_at TIMESTAMP NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY ip_address VARCHAR(45) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE device_tokens MODIFY user_agent TEXT NULL");
            } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        //
    }
};