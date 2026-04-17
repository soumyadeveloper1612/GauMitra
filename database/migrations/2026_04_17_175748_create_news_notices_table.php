<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_notices', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->longText('description');
            $table->date('notice_date')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->string('status', 20)->default('active');   // active, inactive, deleted
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('notice_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_notices');
    }
};