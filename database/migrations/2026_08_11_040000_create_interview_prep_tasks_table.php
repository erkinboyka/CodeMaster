<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_prep_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('category')->default('general');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->text('statement')->nullable();
            $table->text('input_spec')->nullable();
            $table->text('output_spec')->nullable();
            $table->longText('starter_cpp')->nullable();
            $table->longText('starter_python')->nullable();
            $table->longText('starter_c')->nullable();
            $table->longText('starter_csharp')->nullable();
            $table->longText('starter_java')->nullable();
            $table->longText('tests_json')->nullable();
            $table->integer('time_limit_sec')->default(5);
            $table->integer('memory_limit_kb')->default(262144);
            $table->text('hints')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('source_task_id')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('difficulty');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_prep_tasks');
    }
};
