<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaushala_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gaushala_id')
                ->constrained('gaushalas')
                ->cascadeOnDelete();

            $table->string('member_name', 150);
            $table->string('member_phone', 20);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            $table->index(['gaushala_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaushala_members');
    }
};