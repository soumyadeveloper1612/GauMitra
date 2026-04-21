<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permission_role')) {
            if (!Schema::hasColumn('permission_role', 'created_at') || !Schema::hasColumn('permission_role', 'updated_at')) {
                Schema::table('permission_role', function (Blueprint $table) {
                    if (!Schema::hasColumn('permission_role', 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!Schema::hasColumn('permission_role', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }

        if (Schema::hasTable('admin_role_user')) {
            if (!Schema::hasColumn('admin_role_user', 'created_at') || !Schema::hasColumn('admin_role_user', 'updated_at')) {
                Schema::table('admin_role_user', function (Blueprint $table) {
                    if (!Schema::hasColumn('admin_role_user', 'created_at')) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!Schema::hasColumn('admin_role_user', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        //
    }
};