<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            $table->string('platform', 20)->nullable()->after('mobile');
            $table->string('device_id')->nullable()->after('platform');

            $table->index(['mobile', 'purpose']);
            $table->index(['device_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            $table->dropIndex(['mobile', 'purpose']);
            $table->dropIndex(['device_id', 'platform']);

            $table->dropColumn(['platform', 'device_id']);
        });
    }
};