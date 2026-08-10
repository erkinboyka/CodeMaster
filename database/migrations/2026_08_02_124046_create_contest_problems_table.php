<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('points')->default(100);
            $table->text('input_example')->nullable();
            $table->text('output_example')->nullable();
            $table->text('constraints')->nullable();
            $table->text('starter_code')->nullable();
            $table->string('language', 50)->default('python');
            $table->longText('tests_json')->nullable();
            $table->integer('time_limit')->default(2);
            $table->integer('memory_limit')->default(256);
            $table->integer('order_num')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_problems');
    }
};
