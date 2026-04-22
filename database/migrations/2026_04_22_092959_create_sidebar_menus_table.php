<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidebar_menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('route_name')->nullable();
            $table->string('active_pattern')->nullable();
            $table->string('custom_url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('sidebar_menus')->nullOnDelete();
            $table->string('permission_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_menus');
    }
};