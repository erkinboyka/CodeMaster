<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('problem_id');
            $table->enum('status', ['attempted', 'solved'])->default('attempted');
            $table->unsignedInteger('best_time_ms')->nullable();
            $table->unsignedInteger('best_memory_kb')->nullable();
            $table->unsignedInteger('attempts')->default(1);
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'problem_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_user');
    }
};
