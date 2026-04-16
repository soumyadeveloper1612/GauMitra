<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('device_tokens') && Schema::hasTable('emergency_case_alerts')) {
            Schema::table('emergency_case_alerts', function (Blueprint $table) {
                $table->foreign('device_token_id')
                    ->references('id')
                    ->on('device_tokens')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('emergency_case_alerts', function (Blueprint $table) {
            $table->dropForeign(['device_token_id']);
        });
    }
};