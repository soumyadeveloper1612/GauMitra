<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('notification_type', 60)->default('general');
            $table->string('title', 150);
            $table->text('message');

            $table->string('target_scope', 30)->default('all'); // all, area
            $table->json('target_filters')->nullable();

            $table->string('image_url')->nullable();
            $table->string('action_url')->nullable();

            $table->string('related_type')->nullable(); // emergency_case, news_notice, custom
            $table->unsignedBigInteger('related_id')->nullable();

            $table->string('status', 40)->default('pending'); // pending, sending, sent, partially_failed, failed
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('total_devices')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failure_count')->default(0);

            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index('notification_type');
            $table->index('target_scope');
            $table->index('status');
            $table->index('sent_by');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_campaigns');
    }
};