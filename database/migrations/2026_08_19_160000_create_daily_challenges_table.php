<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_challenges', function (Blueprint $table) {
            $table->id();
            $table->date('challenge_date')->unique();
            $table->unsignedBigInteger('problem_id');
            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->unsignedInteger('submissions_count')->default(0);
            $table->unsignedInteger('solved_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_challenges');
    }
};
