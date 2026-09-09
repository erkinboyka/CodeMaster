<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_learning_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('reviewer_id');
            $table->unsignedInteger('reviewee_id');
            $table->unsignedInteger('problem_id');
            $table->json('criterion_scores')->nullable();
            $table->integer('total_score')->default(0);
            $table->integer('max_score')->default(50);
            $table->json('inline_comments')->nullable();
            $table->text('general_feedback')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'reviewer_id']);
            $table->index(['reviewer_id', 'reviewee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_learning_reviews');
    }
};
