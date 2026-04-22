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
            $table->string('menu_key')->unique();
            $table->string('route_name')->nullable();
            $table->string('super_admin_route_name')->nullable();
            $table->string('route_pattern')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('permission_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('sidebar_menus')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_menus');
    }
};