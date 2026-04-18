<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admin_users')) {
            Schema::table('admin_users', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_users', 'is_super_admin')) {
                    $table->boolean('is_super_admin')->default(false)->after('status');
                }

                if (!Schema::hasColumn('admin_users', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admin_users')) {
            Schema::table('admin_users', function (Blueprint $table) {
                if (Schema::hasColumn('admin_users', 'is_super_admin')) {
                    $table->dropColumn('is_super_admin');
                }
            });
        }
    }
};