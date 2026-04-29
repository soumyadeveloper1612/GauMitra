<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            $table->string('platform')->nullable()->change();
            $table->string('device_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('login_otps', function (Blueprint $table) {
            $table->string('platform')->nullable(false)->change();
            $table->string('device_id')->nullable(false)->change();
        });
    }
};
