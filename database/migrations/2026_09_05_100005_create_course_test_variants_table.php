<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_test_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_id');
            $table->string('variant');
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('course_step_tests')->cascadeOnDelete();
        });

        Schema::create('course_test_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_id');
            $table->string('answer');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('course_step_tests')->cascadeOnDelete();
        });

        Schema::create('course_test_matching', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('test_id');
            $table->string('list1_item');
            $table->string('list2_item');
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('course_step_tests')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_test_matching');
        Schema::dropIfExists('course_test_answers');
        Schema::dropIfExists('course_test_variants');
    }
};
