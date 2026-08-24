<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('problem_id');
            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->text('code');
            $table->string('language', 30);
            $table->string('status', 20)->index();
            $table->unsignedInteger('runtime_ms')->nullable();
            $table->unsignedInteger('memory_kb')->nullable();
            $table->unsignedSmallInteger('passed_tests')->default(0);
            $table->unsignedSmallInteger('total_tests')->default(0);
            $table->json('results_json')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'problem_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_submissions');
    }
};
