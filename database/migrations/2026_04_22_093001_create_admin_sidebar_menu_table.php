<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sidebar_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained('admin_users')->cascadeOnDelete();
            $table->foreignId('sidebar_menu_id')->constrained('sidebar_menus')->cascadeOnDelete();
            $table->unique(['admin_user_id', 'sidebar_menu_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sidebar_menu');
    }
};