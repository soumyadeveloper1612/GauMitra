<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            if (!Schema::hasColumn('login_otps', 'platform')) {
                $table->string('platform')->nullable()->after('mobile');
            }

            if (!Schema::hasColumn('login_otps', 'device_id')) {
                $table->text('device_id')->nullable()->after('platform');
            }
        });
    }

    public function down(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            if (Schema::hasColumn('login_otps', 'device_id')) {
                $table->dropColumn('device_id');
            }

            if (Schema::hasColumn('login_otps', 'platform')) {
                $table->dropColumn('platform');
            }
        });
    }
};