<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_step_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('step_id');
            $table->unsignedInteger('skill_id')->nullable();
            $table->enum('type_test', ['one_correct', 'list_correct', 'question_answer', 'true_false', 'matching']);
            $table->text('text');
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('step_id')->references('id')->on('course_steps')->cascadeOnDelete();
            $table->foreign('skill_id')->references('id')->on('course_skills')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_step_tests');
    }
};
