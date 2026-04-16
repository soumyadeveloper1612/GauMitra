<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_case_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emergency_case_id')->constrained('emergency_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('media_type', 50);
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamps();

            $table->index(['emergency_case_id', 'media_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_case_media');
    }
};