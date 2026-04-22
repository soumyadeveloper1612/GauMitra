<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sidebar_menu', function (Blueprint $table) {
            $table->id();

            // IMPORTANT:
            // Change this line only if your admin_users.id is INT instead of BIGINT
            $table->unsignedBigInteger('admin_user_id');

            $table->unsignedBigInteger('sidebar_menu_id');
            $table->timestamps();

            $table->unique(['admin_user_id', 'sidebar_menu_id'], 'admin_sidebar_menu_unique');
        });

        Schema::table('admin_sidebar_menu', function (Blueprint $table) {
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