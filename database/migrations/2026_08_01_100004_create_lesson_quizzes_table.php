<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_quizzes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id')->index();
            $table->text('question_text');
            $table->json('options_json')->comment('Array of option strings');
            $table->integer('correct_option')->comment('Index of correct option');
            $table->text('explanation')->nullable();
            $table->integer('order_num')->default(0);
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_quizzes');
    }
};
