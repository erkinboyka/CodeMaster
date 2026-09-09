<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_students', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('test_id');
            $table->unsignedInteger('step_id');
            $table->unsignedInteger('course_id');
            $table->boolean('is_correct')->default(false);
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('test_id')->references('id')->on('course_step_tests')->cascadeOnDelete();
            $table->foreign('step_id')->references('id')->on('course_steps')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_students');
    }
};
