<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('notification_campaign_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('device_token_id')->nullable();

            $table->string('platform', 30)->nullable();
            $table->text('fcm_token')->nullable();

            $table->string('status', 30)->default('pending'); // sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index('notification_campaign_id');
            $table->index('user_id');
            $table->index('device_token_id');
            $table->index('status');

            $table->foreign('notification_campaign_id')
                ->references('id')
                ->on('notification_campaigns')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');
    }
};