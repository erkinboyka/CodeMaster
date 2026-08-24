<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_problem_topic', function (Blueprint $table) {
            $table->unsignedBigInteger('problem_id');
            $table->unsignedBigInteger('problem_topic_id');
            $table->primary(['problem_id', 'problem_topic_id']);
            $table->foreign('problem_id')->references('id')->on('problems')->onDelete('cascade');
            $table->foreign('problem_topic_id')->references('id')->on('problem_topics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_problem_topic');
    }
};
