<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sidebar_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_user_id');
            $table->unsignedInteger('sidebar_menu_id');
            $table->timestamps();

            $table->unique(['admin_user_id', 'sidebar_menu_id'], 'admin_sidebar_menu_unique');

            $table->foreign('admin_user_id')
                ->references('id')
                ->on('admin_users')
                ->onDelete('cascade');

            $table->foreign('sidebar_menu_id')
                ->references('id')
                ->on('sidebar_menus')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sidebar_menu');
    }
};