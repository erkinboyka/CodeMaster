<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->unsignedInteger('points')->default(10);
            $table->unsignedInteger('solved_count')->default(0);
            $table->unsignedInteger('attempt_count')->default(0);
            $table->string('input_example')->nullable();
            $table->string('output_example')->nullable();
            $table->text('constraints')->nullable();
            $table->text('starter_code')->nullable();
            $table->string('language')->default('python');
            $table->json('tests_json')->nullable();
            $table->unsignedInteger('time_limit')->default(2);
            $table->unsignedInteger('memory_limit')->default(256);
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
