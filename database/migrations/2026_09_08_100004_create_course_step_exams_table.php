<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_step_exams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->index();
            $table->unsignedInteger('step_id')->index();
            $table->enum('type', ['quiz', 'test', 'practice'])->default('quiz');
            $table->text('question');
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('difficulty')->default('medium');
            $table->integer('score')->default(10);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('step_id')->references('id')->on('course_steps')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_step_exams');
    }
};
