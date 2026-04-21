<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_role_user')) {
            Schema::create('admin_role_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_user_id');
                $table->unsignedBigInteger('role_id');
                $table->timestamps();

                $table->unique(['admin_user_id', 'role_id']);

                $table->foreign('admin_user_id')
                    ->references('id')
                    ->on('admin_users')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
            });
        } else {
            if (!Schema::hasColumn('admin_role_user', 'created_at')) {
                Schema::table('admin_role_user', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable();
                });
            }

            if (!Schema::hasColumn('admin_role_user', 'updated_at')) {
                Schema::table('admin_role_user', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // keep safe for production
    }
};