<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_step_vocabulary', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('step_id');
            $table->unsignedInteger('course_id');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->integer('experience')->default(0);
            $table->timestamps();

            $table->foreign('step_id')->references('id')->on('course_steps')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });

        Schema::create('course_step_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('step_id')->nullable();
            $table->unsignedInteger('vocabulary_id')->nullable();
            $table->string('link');
            $table->timestamps();

            $table->foreign('step_id')->references('id')->on('course_steps')->cascadeOnDelete();
            $table->foreign('vocabulary_id')->references('id')->on('course_step_vocabulary')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_step_links');
        Schema::dropIfExists('course_step_vocabulary');
    }
};
