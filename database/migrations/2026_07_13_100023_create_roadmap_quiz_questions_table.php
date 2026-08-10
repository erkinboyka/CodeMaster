<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_quiz_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('node_id')->index();
            $table->text('question');
            $table->text('options');
            $table->string('correct_answer', 255);
            $table->timestamps();

            $table->foreign('node_id')->references('id')->on('roadmap_nodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_quiz_questions');
    }
};
