<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_question_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quiz_question_id')->index();
            $table->string('locale', 5)->index();
            $table->text('question_text');
            $table->text('hint')->nullable();
            $table->timestamps();

            $table->foreign('quiz_question_id', 'qqt_fk')->references('id')->on('quiz_questions')->onDelete('cascade');
            $table->unique(['quiz_question_id', 'locale'], 'qq_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_question_translations');
    }
};
